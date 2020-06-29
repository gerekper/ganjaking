<?php
/**
 * @version 1.0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wc_posts;

?>

	<div class="list">
		<?php
		$last_product = wc_get_product( end( $wc_posts ) );

		foreach ( $wc_posts as $post ) {
			// create the product object
			$product = wc_get_product( $post );

			$row_class = $product->get_id() === $last_product->get_id() ? 'row last-row' : 'row';

			$list_image_size = apply_filters( 'wc_store_catalog_pdf_download_list_image_size', array( 250, 250, true ), $product );
			$product_image   = WC_Store_Catalog_PDF_Download_Ajax::get_product_image( $product, $list_image_size );
		?>
			<div class="<?php echo esc_attr( $row_class ); ?>">

				<div class="image">
					<?php do_action( 'wc_store_catalog_pdf_download_before_product', $product ); ?>

					<a href="<?php echo get_permalink( $product->get_id() ); ?>"><?php echo apply_filters( 'wc_store_catalog_pdf_download_show_product_image', has_post_thumbnail( $post ) ? $product_image : WC_Store_Catalog_PDF_Download_Ajax::get_placeholder_image( $list_image_size ), $product ); ?></a>
				</div>

				<div class="content">
					<h2><a href="<?php echo get_permalink( $product->get_id() ); ?>"><?php echo apply_filters( 'wc_store_catalog_pdf_download_show_product_title', version_compare( WC_VERSION, '3.0', '<' ) ? $product->post->post_title : $product->get_name(), $product ); ?></a></h2>
					
					<?php echo apply_filters( 'wc_store_catalog_pdf_download_show_product_price', $product->get_price_html() ); ?>
					
					<div class="product-description">
					<?php echo apply_filters( 'wc_store_catalog_pdf_download_description', ( version_compare( WC_VERSION, '3.0', '<' ) ? $product->post->post_content : $product->get_description() ), $product ); ?>
					</div>

					<?php apply_filters( 'wc_store_catalog_pdf_download_product_meta', include( WC_Store_Catalog_PDF_Download_Ajax::get_product_meta_template( $product ) ) ); ?>
					
					<?php do_action( 'wc_store_catalog_pdf_download_product_attr', $product ); ?>

					<?php do_action( 'wc_store_catalog_pdf_download_after_product', $product ); ?>
				</div>
				
			</div>
		<?php
		}
		?>
	</div>
