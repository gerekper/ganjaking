<?php
if (!defined('ABSPATH')) {
	exit;
}

if ( isset( $product_id ) && isset( $quantity ) ){ ?>

	<style>
		table {
			border-spacing: 10px;
		}

		td.main-barcode-container{
			padding: 10px;
		}
		.ywbc-barcode-display-container{
			text-align: center;
		}
	</style>

	<table>

<?php

$item_ids = array();

$number_of_columns = apply_filters( 'ywbc_print_by_product_number_of_columns', 4);

for ( $i = 0; $i < $quantity; $i++ ){
	$item_ids[$i] = $product_id;
}

		foreach ( array_chunk($item_ids, $number_of_columns) as $row ) { ?>

			<tr>

				<?php  foreach ($row as $product_id ) { ?>

					<?php

					$product = wc_get_product( $product_id );


					?>

					<td  class="main-barcode-container">
						<?php YITH_YWBC()->show_barcode( $product_id, '1', '', '' ); ?>
					</td>


				<?php } ?>

			</tr>

		<?php } ?></table>

	<?php

}





