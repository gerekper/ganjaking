<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $posts ) : ?>
	<h3><?php echo $title; ?></h3>

	<table class="shop_table shop_table_responsive ywbc-search-by-products">
		<thead>
		<tr class="ywbc-search-product-row-title">
            <th class="ywbc-product-thumbnail">
                <span class="nobr"><?php echo esc_html__( 'Product image', 'yith-woocommerce-barcodes' ); ?></span>
            </th>
			<th class="ywbc-product-title" style="width: 25%">
				<span class="nobr"><?php echo esc_html__( 'Product', 'yith-woocommerce-barcodes' ); ?></span>
			</th>

			<th class="ywbc-barcode-value" style="width: 20%">
				<span class="nobr"><?php echo esc_html__( 'Barcode', 'yith-woocommerce-barcodes' ); ?></span>
			</th>

			<th class="ywbc-product-status" style="width: 20%">
				<span class="nobr"><?php echo esc_html__( 'Stock status', 'yith-woocommerce-barcodes' ); ?></span>
			</th>

			<th class="ywbc-barcode-action">
			</th>
		</tr>
		</thead>

		<tbody>
		<?php foreach ( $posts as $post ) {
			$product = wc_get_product( $post );
			if ( $product ) {
				wc_get_template( 'shortcode/ywbc-search-products-row.php',
					array(
						'object'          => $product,
						'barcode_actions' => $barcode_actions,
					),
					'',
					YITH_YWBC_TEMPLATES_DIR
				);
			}
		} ?>
		</tbody>
	</table>
<?php else: ?>
	<span><?php esc_html_e( 'No product matches the selected criteria', 'yith-woocommerce-barcodes' ); ?></span>
<?php endif;
