<?php
if (!defined('ABSPATH')) {
	exit;
}

$action = isset($option['action']) ? $option['action'] : 'print_product_barcodes_pdf';
$desc = isset($option['desc']) ? $option['desc'] : '';
$name = isset($option['name']) ? $option['name'] : '';

$show_images = get_option ( 'tool_print_barcodes_show_image', 'no' );

$post_types = 'include_variations' == get_option ( 'ywbc_enable_print_barcodes_variations', 'all_products' ) ? array( 'product', 'product_variation' ) : 'product';

$query_args = apply_filters('yith_barcode_query_args_get_products',array(
		'post_type' => apply_filters('yith_barcode_query_post_type', $post_types ),
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'fields' => 'ids',
		'meta_query' => array(
			array(
				'key' => YITH_Barcode::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE,
				'compare' => 'EXISTS'
			)
		)
	)
);

$products = get_posts($query_args);

$json_product_ids = array();

foreach ($products as $product) {
	$json_product_ids[] = $product;
}
$json_product_ids = esc_attr(json_encode($json_product_ids));


?>

	<tr valign="top" class="ywbc-print-barcode-tr">
		<th scope="row" class="titledesc"><?php esc_attr_e($name); ?></th>

		<td class="forminp" style="padding: 0px 20px;">

			<input type="button" class="button button-primary ywbc-print-barcode"
			       data-barcode_action="<?php esc_attr_e($action); ?>" data-item_ids="<?php echo $json_product_ids; ?>"
			       value="<?php echo sprintf ( esc_html__('Print', 'yith-woocommerce-barcodes') ); ?>">
			<span class="description"><?php esc_attr_e($desc); ?></span>
		</td>
	</tr>

<div id="aux-print-barcodes"></div>
