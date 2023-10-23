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
$name   = false;
$value  = false;
$expiry = false;

$wc_order   = wc_get_order( $order_id );
$order_date = $wc_order ? ( $wc_order->get_date_completed() ? $wc_order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false ) : false;


if ( empty( $warranty['label'] ) ) {
	$product_warranty  = warranty_get_product_warranty( $item['product_id'] );
	$warranty['label'] = $product_warranty['label'];
}

if ( 'addon_warranty' === $warranty['type'] ) {
	$addons = $warranty['addons'];

	$warranty_index = wc_get_order_item_meta( $item_id, '_item_warranty_selected', true );

	if ( false !== $warranty_index && isset( $addons[ $warranty_index ] ) && ! empty( $addons[ $warranty_index ] ) ) {
		$addon = $addons[ $warranty_index ];
		$name  = $warranty['label'];
		$value = $GLOBALS['wc_warranty']->get_warranty_string( $addon['value'], $addon['duration'] );

		if ( $order_date ) {
			$expiry = warranty_get_date( $order_date, $addon['value'], $addon['duration'] );
		}
	}
} elseif ( 'included_warranty' === $warranty['type'] ) {
	if ( 'limited' === $warranty['length'] ) {
		$name  = $warranty['label'];
		$value = $GLOBALS['wc_warranty']->get_warranty_string( $warranty['value'], $warranty['duration'] );

		if ( $order_date ) {
			$expiry = warranty_get_date( $order_date, $warranty['value'], $warranty['duration'] );
		}
	} elseif ( 'lifetime' === $warranty['length'] ) {
		$name  = $warranty['label'];
		$value = __( 'Lifetime', 'wc_warranty' );
	}
}

if ( ! $name || ! $value ) {
	return;
}

?>
<table cellspacing="0" class="display_meta">
	<tr>
		<th><?php echo wp_kses_post( $name ); ?>:</th>
		<td>
		<?php
		echo wp_kses_post( $value );
		echo $expiry ? ' <small>(' . ( current_time( 'timestamp' ) > strtotime( $expiry ) ? 'expired' : 'expires' ) . ' ' . esc_html( $expiry ) . ')</small>' : '';
		?>
		</td>
	</tr>
</table>
