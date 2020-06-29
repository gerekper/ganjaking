<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shipment Tracking
 *
 * Shows tracking information in the HTML order email
 *
 * @author  WooThemes
 * @package WooCommerce Shipment Tracking/templates/email
 * @version 1.6.4
 */
?>

<h3><?php echo apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( 'Tracking Information', 'woocommerce-shipment-tracking' ) ); ?></h3>

<?php foreach ( $items as $item ) : ?>
<p class="tracking-content">
	<strong><?php echo esc_html( $item['formatted_tracking_provider'] ); ?></strong>
	<?php if ( strlen( $item['formatted_tracking_link'] ) > 0 ) : ?>
		- <?php echo sprintf( '<a href="%s" target="_blank" title="' . esc_attr( __( 'Click here to track your shipment', 'woocommerce-shipment-tracking' ) ) . '">' . __( 'Track', 'woocommerce-shipment-tracking' ) . '</a>', $item['formatted_tracking_link'] ); ?>
	<?php endif; ?>
	<br/>
	<em><?php echo esc_html( $item['tracking_number'] ); ?></em>
	<br />
	<?php /* translators: 1: date of shipping */ ?>
	<span style="font-size: 0.8em"><?php echo esc_html( sprintf( __( 'Shipped on %s', 'woocommerce-shipment-tracking' ), date_i18n( 'Y-m-d', $item['date_shipped'] ) ) ); ?></span>
</p>
<?php endforeach; ?>
