<?php
if (!defined('ABSPATH')) {
    exit;
}

$action = isset($option['action']) ? $option['action'] : 'apply_barcode_to_products';
$desc = isset($option['desc']) ? $option['desc'] : '';
$name = isset($option['name']) ? $option['name'] : '';

$post_types = array( 'product', 'product_variation' );

$meta_query = 'regenerate' == get_option ( 'ywbc_tool_regenerate_barcodes', 'generate' ) ? array() :
	array(
		array(
			'key' => YITH_Barcode::YITH_YWBC_META_KEY_BARCODE_DISPLAY_VALUE,
			'compare' => 'NOT EXISTS'
		)
	);

$query_args = apply_filters('yith_barcode_query_args_get_products',array(
            'post_type' => apply_filters('yith_barcode_query_post_type', $post_types ),
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => $meta_query
        )
);



$products = get_posts($query_args);

$json_product_ids = array();

foreach ($products as $product) {
    $json_product_ids[] = $product;
}
$json_product_ids = esc_attr(json_encode($json_product_ids));
?>

<tr valign="top">
    <th scope="row" class="titledesc"><?php esc_attr_e($name); ?></th>
    <td class="forminp" colspan="1" style="padding: 0px 20px;">

        <input type="button" class="button button-primary ywbc_apply_barcode"
               data-barcode_action="<?php esc_attr_e($action); ?>" data-item_ids="<?php echo $json_product_ids; ?>"
               value="<?php echo __('Generate barcodes', 'yith-woocommerce-barcodes'); ?>">
        <span class="description"><?php esc_attr_e($desc); ?></span>

	    <br>

	    <div class="ywbc_apply_messages">
			<span class="ywbc_apply_icon"></span>
			<span class="ywbc_apply_text"></span>
		</div>

		<div class="ywbc_apply_progressbar" id="ywbc_apply_progressbar_all" style="width: 25%">
			<div class="ywbc_apply_progressbar_percent" id="ywbc_apply_progressbar_percent_all"></div>
		</div>
	</td>
</tr>


