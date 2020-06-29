<?php
if (!defined('ABSPATH')) {
	exit;
}

//$inline_css = '.ywbc-barcode-display-value {margin-left: 10px;}';

?>

	<style>
		table {
			border: 0.5px solid grey;
			border-collapse: collapse;
			border-spacing: 25px;
		}
		tr {
			border-bottom: 0.5px solid grey;
		}
		td.main-barcode-container{
			border-right: 0.5px solid grey;
			padding: 20px;
		}
		.ywbc-barcode-display-container{
			text-align: center;
		}
		.product-image{
			padding: 20px;
		}
	</style>


	<table>


<?php

$number_of_columns = apply_filters( 'ywbc_print_all_product_number_of_columns', 2);


if ( isset( $item_ids ) && is_array( $item_ids ) ){

	foreach ( array_chunk($item_ids, $number_of_columns) as $row ) { ?>

		<tr>

			<?php  foreach ($row as $product_id ) { ?>

				<?php

				$product = wc_get_product( $product_id );

				if ( is_object($product) ) {
					$upload_dir = wp_upload_dir ();
					$image_path = $product->get_image_id () ? current ( wp_get_attachment_image_src ( $product->get_image_id (),
						'thumbnail' ) ) : wc_placeholder_img_src ( 'thumbnail' );
				}
				else{
					$image_path = wc_placeholder_img_src ( 'thumbnail' );
				}
				?>

				<?php if ( get_option( 'tool_print_barcodes_show_image', 'no' ) == 'yes' ): ?>
					<?php if ( $image_path ): ?>
						<td class="image-container" >
							<img class="product-image" src="<?php echo $image_path; ?>" style="width: 50px; height: 50px" />
						</td>
					<?php endif; ?>
				<?php endif; ?>

				<td class="image-container">
					<div style="text-align: center; font-size: 12px"><?php echo $product->get_name() ?></div>
					<div style="text-align: center;font-size: 12px"><?php echo $product->get_sku() ?></div>
				</td>

				<td  class="main-barcode-container">
					<?php YITH_YWBC()->show_barcode( $product_id, '1', '', '' ); ?>
				</td>


			<?php } ?>

		</tr>

	<?php }


	?></table><?php

}

