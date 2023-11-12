<?php
/**
 * UAEL WooCommerce Products - Featured Flash.
 *
 * @package UAEL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post, $product;

$featured_text = __( 'New', 'uael' );

if ( '' !== $this->get_instance_value( 'featured_flash_string' ) ) {
	$featured_text = $this->get_instance_value( 'featured_flash_string' );
}

?>
<?php if ( $product->is_featured() ) : ?>

	<?php
		$featured_filter = apply_filters( 'uael_woo_products_featured_flash', '<div class="uael-featured-flash-wrap"><span class="uael-featured">' . wp_kses_post( $featured_text ) . '</span></div>', $post, $product );
		echo wp_kses_post( $featured_filter );
	?>
	<?php
endif;

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
