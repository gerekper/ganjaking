<?php
/**
 * @version 1.0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wc_posts;

?>

	<div class="single">
		<?php

		foreach ( $wc_posts as $post ) {

			// create the product object
			$product = wc_get_product( $post );

			$single_image_size = apply_filters( 'wc_store_catalog_pdf_download_single_image_size', array( 800, 9999, false ), $product );
			$product_image     = WC_Store_Catalog_PDF_Download_Ajax::get_product_image( $product, $single_image_size );
		?>
			<?php do_action( 'wc_store_catalog_pdf_download_before_product', $product ); ?>

			<h2><a href="<?php echo get_permalink( $product->get_id() ); ?>"><?php echo apply_filters( 'wc_store_catalog_pdf_download_show_product_title', ( version_compare( WC_VERSION, '3.0', '<' ) ? $product->post->post_title : $product->get_name() ), $product ); ?></a></h2><br /><br />
			<p><a href="<?php echo get_permalink( $product->get_id() ); ?>" class="single-image-link"><?php echo apply_filters( 'wc_store_catalog_pdf_download_show_product_image', has_post_thumbnail( $post ) ? $product_image : WC_Store_Catalog_PDF_Download_Ajax::get_placeholder_image( array() ), $product ); ?></a></p>

			<div class="clear"></div>

			<?php echo apply_filters( 'wc_store_catalog_pdf_download_show_product_price', $product->get_price_html() ); ?>
			
			<p class="description"><?php echo apply_filters( 'wc_store_catalog_pdf_download_description', ( version_compare( WC_VERSION, '3.0', '<' ) ? $product->post->post_content : $product->get_description() ), $product ); ?></p>

			<?php apply_filters( 'wc_store_catalog_pdf_download_product_meta', include( WC_Store_Catalog_PDF_Download_Ajax::get_product_meta_template( $product ) ) ); ?>

			<?php do_action( 'wc_store_catalog_pdf_download_product_attr', $product ); ?>

			<?php do_action( 'wc_store_catalog_pdf_download_after_product', $product ); ?>
		<?php
		}
		?>
	</div>
