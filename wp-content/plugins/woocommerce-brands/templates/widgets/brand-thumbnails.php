<?php
/**
 * Show a grid of thumbnails
 */

$wrapper_class = 'fluid-columns';
if ( ! $fluid_columns && in_array( $columns, array( 1, 2, 3, 4, 5, 6 ) ) ) {
	$wrapper_class = 'columns-' .  $columns;
}
?>
<ul class="brand-thumbnails <?php echo esc_attr( $wrapper_class ); ?>">

<?php
	foreach ( array_values( $brands ) as $index => $brand ) :
		$class = '';
		if ( $index == 0 || $index % $columns == 0 ) {
			$class = 'first';
		} else if ( ( $index + 1 ) % $columns == 0 ) {
			$class = 'last';
		}
		?>

		<li class="<?php echo esc_attr( $class ); ?>">
			<a href="<?php echo esc_url( get_term_link( $brand->slug, 'product_brand' ) ); ?>" title="<?php echo esc_attr( $brand->name ); ?>">
				<?php echo get_brand_thumbnail_image( $brand ); ?>
			</a>
		</li>

<?php endforeach; ?>

</ul>
