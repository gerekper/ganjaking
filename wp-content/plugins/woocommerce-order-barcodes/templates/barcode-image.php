<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Variables passed here from WooCommerce_Order_Barcodes::display_barcode():
 *
 * @var string $barcode_text
 * @var string $barcode_url
 * @var string $foreground_color
 */

?>

<img
	src="<?php echo esc_url( $barcode_url ) ?>"
	title="<?php echo esc_attr__( 'Barcode', 'woocommerce-order-barcodes' ) ?>"
	alt="<?php echo esc_attr__( 'Barcode', 'woocommerce-order-barcodes' ) ?>"
	style="display:inline;border:0;max-width:100%"
/>
<br/>
<span
	style="<?php echo esc_attr( 'color: ' . $foreground_color . ';font-family:monospace;' ) ?>"
>
	<?php echo esc_html( $barcode_text ) ?>
</span>
