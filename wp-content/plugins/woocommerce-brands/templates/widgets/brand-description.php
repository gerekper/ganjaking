<?php
/**
 * Show a brands description when on a taxonomy page
 */
?>
<?php global $woocommerce; ?>

<?php if ( $thumbnail ) : ?>

	<?php echo get_brand_thumbnail_image( $brand ) ?>

<?php endif; ?>

<?php echo wpautop( wptexturize( term_description() ) ); ?>
