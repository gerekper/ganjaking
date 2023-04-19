<?php
/**
 * Show a brands description when on a taxonomy page
 */
?>
<?php global $woocommerce; ?>

<?php if ( $thumbnail ) : ?>

	<?php echo get_brand_thumbnail_image( $brand ); // phpcs:ignore WordPress.Security.EscapeOutput ?>

	<?php

endif;

echo wp_kses_post( wpautop( wptexturize( term_description() ) ) );
