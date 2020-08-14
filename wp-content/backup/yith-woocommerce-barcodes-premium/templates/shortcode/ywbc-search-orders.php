<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( $posts ) : ?>
	<h3><?php echo $title; ?></h3>
	
	<table class="shop_table shop_table_responsive ywbc-search-by-orders">
		<thead>
		<tr class="ywbc-search-order-row-title">
			<th class="ywbc-order-title">
				<span class="nobr"><?php echo esc_html__( 'Order', 'yith-woocommerce-barcodes' ); ?></span>
			</th>
			
			<th class="ywbc-order-status">
				<span class="nobr"><?php echo esc_html__( 'Status', 'yith-woocommerce-barcodes' ); ?></span>
			</th>
			
			<th class="ywbc-barcode-value">
				<span class="nobr"><?php echo esc_html__( 'Barcode', 'yith-woocommerce-barcodes' ); ?></span>
			</th>

            <?php if ( !empty($barcode_actions)) { ?>
			<th class="ywbc-barcode-action">
			</th>
            <?php } ?>

		</tr>
		</thead>
		
		<tbody>
		<?php foreach ( $posts as $post ) {
			$order = wc_get_order( $post );
			if ( $order ) {
				wc_get_template( 'shortcode/ywbc-search-orders-row.php',
					array(
						'object'           => $order,
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
	<span><?php esc_html_e( 'No order matches the selected criteria', 'yith-woocommerce-barcodes' ); ?></span>
<?php endif;