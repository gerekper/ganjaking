<?php
/**
 * Tracking info email template.
 * Shows tracking information in the HTML order email
 *
 * @package WC_Shipment_Tracking
 * @version 1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<h3>
	<?php
	/**
	 * Filter to change the content title of the email.
	 *
	 * @since 1.3.7
	 * @param String Content title.
	 */
	echo esc_html( apply_filters( 'woocommerce_shipment_tracking_my_orders_title', __( 'Tracking Information', 'woocommerce-shipment-tracking' ) ) );
	?>
</h3>

<?php foreach ( $items as $item ) : ?>
<p class="tracking-content">
<strong><?php echo esc_html( $item['formatted_tracking_provider'] ); ?></strong>
	<?php if ( strlen( $item['formatted_tracking_link'] ) > 0 ) : ?>
		- <?php echo sprintf( '<a href="%s" target="_blank" title="%s">%s</a>', esc_url( $item['formatted_tracking_link'] ), esc_attr__( 'Click here to track your shipment', 'woocommerce-shipment-tracking' ), esc_html__( 'Track', 'woocommerce-shipment-tracking' ) ); ?>
	<?php endif; ?>
	<br/>
	<em><?php echo esc_html( $item['tracking_number'] ); ?></em>
	<br />
	<?php /* translators: 1: date of shipping */ ?>
	<span style="font-size: 0.8em"><?php echo sprintf( esc_html__( 'Shipped on %s', 'woocommerce-shipment-tracking' ), esc_html( date_i18n( wc_date_format(), $item['date_shipped'] ) ) ); ?></span>
</p>
<?php endforeach; ?>
