<?php
/**
 * @version 1.0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wc_posts;

?>

	<table class="grid">
		<?php

		$i = 0;
		$columns = apply_filters( 'wc_store_catalog_pdf_download_grid_columns', 3 );

		foreach ( $wc_posts as $post ) {
			$i++;

			// create the product object
			$product = wc_get_product( $post );

			$grid_image_size = apply_filters( 'wc_store_catalog_pdf_download_grid_image_size', array( 150, 150, true ), $product );
			$product_image   = WC_Store_Catalog_PDF_Download_Ajax::get_product_image( $product, $grid_image_size );

			// sets 3 items per row
			if ( 0 === ( $i - 1 ) % $columns || 1 === $i ) {
				echo '<tr>';
			}
		?>

			<td>				
				<?php do_action( 'wc_store_catalog_pdf_download_before_product', $product ); ?>

				<a href="<?php echo get_permalink( $product->get_id() ); ?>"><?php echo apply_filters( 'wc_store_catalog_pdf_download_show_product_image', has_post_thumbnail( $post ) ? $product_image : WC_Store_Catalog_PDF_Download_Ajax::get_placeholder_image( $grid_image_size ), $product ); ?></a>
				
				<h2><a href="<?php echo get_permalink( $product->get_id() ); ?>"><?php echo apply_filters( 'wc_store_catalog_pdf_download_show_product_title', version_compare( WC_VERSION, '3.0', '<' ) ? $product->post->post_title : $product->get_name(), $product ); ?></a></h2>
				
				<?php echo apply_filters( 'wc_store_catalog_pdf_download_show_product_price', $product->get_price_html() ); ?>				

				<?php do_action( 'wc_store_catalog_pdf_download_after_product', $product ); ?>
			</td>
			<?php
			if ( 0 === $i % $columns ) {
				echo '</tr>';
			}
		}
		?>
	</table>
