<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php
/**
 * @var $order \WC_Order
 * @var $order_id int
 * @var $idxs array
 *
 * @see \Warranty_Shortcodes::render_warranty_request_shortcode for extracted variable definitions
 */
?>
<form name="warranty_form" id="warranty_form" method="POST" action="" enctype="multipart/form-data">

	<?php
	$request_data = warranty_request_data();
	if ( isset( $request_data['error'] ) ) :
		?>
		<ul class="woocommerce_error">
			<li><?php echo esc_html( $request_data['error'] ); ?></li>
		</ul>
		<?php
	endif;

	$request_errors = array();
	if ( ! empty( $request_data['errors'] ) ) {
		$request_errors = json_decode( $request_data['errors'], true );
	}

	if ( ! empty( $request_errors ) ) {
		echo '<div class="woocommerce-error">';
		esc_html_e( 'The following errors were found while processing your request:', 'wc_warranty' );
		echo '<ul>';

		foreach ( $request_errors as $request_error ) {
			echo '<li>' . esc_html( $request_error ) . '</li>';
		}

		echo '</ul>';
		echo '</div>';
	}

	foreach ( $idxs as $idx ) {
		$item      = ! empty( $items[ $idx ] ) ? $items[ $idx ] : false;
		$variation = warranty_get_variation_string( $order, $item );

		if ( $item && $item['qty'] >= 1 ) :
			$product = $item->get_product();
			$max     = warranty_get_quantity_remaining( $order_id, $product->get_id(), $idx );
			?>
			<div class="wfb-field-div wfb-field-div-select">
				<label>
					<?php
					echo esc_html( $item['name'] );

					if ( $variation ) {
						echo '<div class="item-variations">' . $variation . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</label>
				<select name="warranty_qty[<?php echo esc_attr( $idx ); ?>]" class="wfb-field">
					<?php for ( $x = 1; $x <= $max; $x ++ ) : ?>
						<option value="<?php echo esc_attr( $x ); ?>"><?php echo esc_html( $x ); ?></option>
					<?php endfor; ?>
				</select>
			</div>
			<?php
		endif;

		echo '<input type="hidden" name="idx[]" value="' . esc_attr( $idx ) . '" />';
	}

	echo '<hr/>';

	$refunds_allowed = warranty_refund_requests_enabled();
	$coupons_allowed = warranty_coupon_requests_enabled();

	if ( $refunds_allowed || $coupons_allowed ) :
		?>
		<div class="wfb-field-div wfb-field-div-select">
			<label><?php esc_html_e( 'I want to request for a', 'wc_warranty' ); ?></label>

			<select name="warranty_request_type" class="wfb-field">
				<option value="replacement"><?php esc_html_e( 'Replacement item', 'wc_warranty' ); ?></option>
				<?php if ( $refunds_allowed ) : ?>
					<option value="refund"><?php esc_html_e( 'Refund', 'wc_warranty' ); ?></option>
				<?php endif; ?>
				<?php if ( $coupons_allowed ) : ?>
					<option value="coupon"><?php esc_html_e( 'Refund as store credit', 'wc_warranty' ); ?></option>
				<?php endif; ?>
			</select>
		</div>
		<?php
	else :
		echo '<input type="hidden" name="warranty_request_type" value="replacement" />';
	endif;

	$request_tracking_code = get_option( 'warranty_show_tracking_field', 'no' );

	if ( 'yes' === $request_tracking_code ) :
		?>
		<div class="wfb-field-div wfb-field-div-select">
			<label><?php esc_html_e( 'Return Shipping Tracking', 'wc_warranty' ); ?></label>

			<select class="tracking_provider wfb-field" name="tracking_provider">
				<?php
				foreach ( WooCommerce_Warranty::get_providers() as $provider_group => $providers ) {
					echo '<optgroup label="' . esc_attr( $provider_group ) . '">';
					foreach ( $providers as $provider => $url ) {
						$selected = isset( $request['tracking_provider'] ) && ( sanitize_title( $provider ) === $request['tracking_provider'] ) ? 'selected' : '';
						echo '<option value="' . esc_attr( sanitize_title( $provider ) ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $provider ) . '</option>';
					}
					echo '</optgroup>';
				}
				?>
			</select> <input type="text" class="tracking_code" name="tracking_code" value="" placeholder="<?php esc_attr_e( 'Tracking code', 'wc_warranty' ); ?>" />
		</div>


		<?php
	endif;

	WooCommerce_Warranty::render_warranty_form();
	?>
	<p>
		<input type="hidden" name="req" value="new_warranty" /> <input type="hidden" name="order" value="<?php echo esc_attr( $order->get_id() ); ?>" />
		<input type="submit" name="submit" value="<?php esc_attr_e( 'Submit', 'wc_warranty' ); ?>" class="button">
		<?php wp_nonce_field( 'wc_warranty_new_warranty_nonce', 'wc_new_warranty_nonce' ); ?>
	</p>

</form>
