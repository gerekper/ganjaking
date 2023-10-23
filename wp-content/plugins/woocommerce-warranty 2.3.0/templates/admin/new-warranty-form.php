<?php
/**
 * The template for displaying warranty options.
 *
 * @package WooCommerce_Warranty\Templates
 * @version 2.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $this_order ) || ! $this_order instanceof WC_Order ) {
	echo '<p>' . esc_html__( 'Please select a valid order.', 'wc_warranty' ) . '</p>';

	return;
}
?>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
	<?php
	if ( isset( $_GET['search_key'] ) ) {
		check_admin_referer( 'warranty_create' );
	}
	$order_id  = $this_order->get_id();
	$idx_array = isset( $_GET['idx'] ) ? wc_clean( wp_unslash( $_GET['idx'] ) ) : array();

	/**
	 * @var WC_Order_Item_Product[] $items WC Order Item.
	 */
	foreach ( $idx_array as $idx ) :
		$item = ( isset( $items[ $idx ] ) ) ? $items[ $idx ] : false;

		if ( ! $item ) {
			continue;
		}

		$variation = warranty_get_variation_string( $this_order, $item );

		$max = $has_warranty && $item->get_quantity() > 1 ? warranty_get_quantity_remaining( $order_id, $item->get_product_id(), $idx ) : $item->get_quantity() - warranty_count_quantity_used( $order_id, $item->get_product_id(), $idx );
		?>
		<div class="wfb-field-div wfb-field-div-select">
			<label>
				<?php
				echo esc_html( $item->get_name() );
				echo $variation ? '<div class="item-variations">' . $variation . '</div>' : ''; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped.
				?>
			</label>
			<select name="warranty_qty[<?php echo esc_attr( $idx ); ?>]" class="wfb-field">
				<?php for ( $x = 1; $x <= $max; $x ++ ) : ?>
					<option value="<?php echo esc_attr( $x ); ?>"><?php echo esc_html( $x ); ?></option>
				<?php endfor; ?>
			</select>
		</div>
	<?php
	endforeach;

	$refunds_allowed = warranty_refund_requests_enabled();
	$coupons_allowed = warranty_coupon_requests_enabled();

	if ( $refunds_allowed || $coupons_allowed ) :
		?>
		<div class="wfb-field-div wfb-field-div-select">
			<label><?php esc_html_e( 'Request type', 'wc_warranty' ); ?></label>

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

	WooCommerce_Warranty::render_warranty_form();

	?>
	<?php wp_nonce_field( 'warranty_create' ); ?>
	<input type="hidden" name="order_id" value="<?php echo esc_attr( $order_id ); ?>" /> <input type="hidden" name="action" value="warranty_create" />
	<input type="submit" name="submit" value="<?php esc_attr_e( 'Submit', 'wc_warranty' ); ?>" class="button">
</form>
