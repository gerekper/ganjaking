<?php
/**
 * Single Brand
 *
 * @usedby [product_brand]
 *
 * @see WC_Brands::output_product_brand()
 *
 * @var WP_Term $term      The term object.
 * @var string  $thumbnail The URL to the brand thumbnail.
 * @var string  $class     The class to apply to the thumbnail image.
 * @var string  $width     The width of the image.
 * @var string  $height    The height of the image.
 *
 * Ignore space indent sniff for this file, as it is used for alignment rather than actual indents.
 * phpcs:ignoreFile Generic.WhiteSpace.DisallowSpaceIndent
 */
?>
<a href="<?php echo esc_url( get_term_link( $term, 'product_brand' ) ); ?>">
	<img src="<?php echo esc_url( $thumbnail ); ?>"
	     alt="<?php echo esc_attr( $term->name ); ?>"
	     class="<?php echo esc_attr( $class ); ?>"
	     style="width: <?php echo esc_attr( $width ); ?>; height: <?php echo esc_attr( $height ); ?>;"/>
</a>
