<?php
if (!defined('ABSPATH')) {
	exit;
}

$action = isset($option['action']) ? $option['action'] : 'print_barcodes_by_product_pdf';


?>
<tr valign="top" class="ywbc-print-barcode-by-product-tr">
	<th scope="row" class="titledesc"><?php esc_attr_e($name); ?></th>

	<td class="forminp ywbc-print-barcode-by-product-id-column">

		<div class="print-by-products-container">

		<p style="display: inline"><?php echo esc_html__( 'Print a document with ', 'yith-woocommerce-barcodes'); ?></p>
		<input type="number" value="1" class="ywbc-print-barcode-by-product-quantity" style="width: 5%; margin: -10px 5px 0px 5px;" min="1" placeholder="<?php echo esc_html__( 'Quantity', 'yith-woocommerce-barcodes'); ?>">
		<p style="display: inline"><?php echo esc_html__( 'barcodes of this product: ', 'yith-woocommerce-barcodes'); ?></p>

		<?php echo yith_plugin_fw_get_field( array(
			'id' => 'print_barcodes_by_product_select',
			'type' => 'ajax-products',
			'data' => array(
				'action' => 'woocommerce_json_search_products_and_variations',
				'security' => wp_create_nonce( 'search-products' )
			),
		) ); ?>

			<input type="button" class="button button-primary ywbc-print-barcode-by-product"
			       data-barcode_action="<?php esc_attr_e($action); ?>" style="margin-top: 8px; "
			       value="<?php echo sprintf ( esc_html__('Print', 'yith-woocommerce-barcodes') ); ?>">
		</div>

		<span class="description"><?php echo __('Choose which product barcode print and how many of them', 'yith-woocommerce-barcodes') ?></span>
	</td>


</tr>

<div id="aux-print-barcodes-by-products"></div>
