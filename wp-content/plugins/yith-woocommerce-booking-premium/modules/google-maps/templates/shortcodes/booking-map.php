<?php
/**
 * Booking map shortcode Template
 *
 * @var array              $coordinates
 * @var WC_Product_Booking $product
 * @var string             $width
 * @var string             $height
 * @var string             $zoom
 * @var string             $type
 *
 * @package YITH\Booking\Templates
 */

defined( 'YITH_WCBK' ) || exit;

wp_enqueue_script( 'yith-wcbk-booking-map' );

$latitude  = $coordinates['lat'] ?? false;
$longitude = $coordinates['lng'] ?? false;

if ( ! $latitude || ! $longitude ) {
	return;
}
?>

<div class="yith-wcbk-booking-map-container">
	<div class="yith-wcbk-booking-map"
			style="width:<?php echo esc_attr( $width ); ?>; height:<?php echo esc_attr( $height ); ?>"
			data-latitude="<?php echo esc_attr( $latitude ); ?>"
			data-longitude="<?php echo esc_attr( $longitude ); ?>"
			data-zoom="<?php echo esc_attr( $zoom ); ?>"
			data-type="<?php echo esc_attr( $type ); ?>">
	</div>
</div>
