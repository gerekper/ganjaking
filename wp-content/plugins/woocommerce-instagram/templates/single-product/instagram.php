<?php
/**
 * Single Product Instagram
 *
 * @package WC_Instagram/Templates
 * @version 3.1.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Global Variables.
 *
 * @global string $hashtag The product hashtag.
 * @global int    $columns The number of columns for the images grid.
 * @global array  $images  An array with the hashtag images.
 */
?>
<section class="woocommerce-instagram">
	<?php
	$heading = sprintf(
		/* translators: %s: product title */
		apply_filters( 'woocommerce_instagram_section_title', __( '%s on Instagram', 'woocommerce-instagram' ) ),
		$product->get_title()
	);

	if ( $heading ) :
		echo '<h2 class="woocommerce-instagram-heading">' . esc_html( $heading ) . '</h2>';
	endif;
	?>

	<?php
	if ( ! empty( $images ) ) :
		wc_instagram_loop_start();

		foreach ( $images as $image ) :
			wc_instagram_image( $image );
		endforeach;

		wc_instagram_loop_end();
	endif;
	?>

	<?php if ( apply_filters( 'woocommerce_instagram_display_action_note', true ) ) : ?>
		<p class="woocommerce-instagram-call-to-action">
			<?php
			echo wp_kses_post(
				sprintf(
					/* translators: 1: product title, 2: product hashtag */
					__( 'Want to share your instagrams of you with your %1$s? Use the %2$s hashtag.', 'woocommerce-instagram' ),
					'<strong>' . esc_html( $product->get_title() ) . '</strong>',
					'<strong>#' . esc_attr( $hashtag ) . '</strong>'
				)
			);
			?>
		</p>
	<?php endif; ?>
</section>
