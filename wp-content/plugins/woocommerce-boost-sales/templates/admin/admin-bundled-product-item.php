<?php
/**
 * Admin Add Bundled Product markup.
 * @version 4.8.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$item_data = ! empty( $item_data ) ? $item_data : array();
$parent    = '';
$product   = wc_get_product( $product_id );
if ( $product->get_type() == 'variable' && $product->has_child() ) {
	$parent = ' (#PARENT)';
}
?>
<div class="wbs-wcpb-bundled-item wc-metabox <?php echo $open_closed ?>" rel="<?php echo $metabox_id; ?>">
	<h3>
		<button type="button" class="wbs-wcpb-remove-bundled-product-item button"><?php echo __( 'Remove', 'woocommerce-boost-sales' ); ?></button>
		<div class="handlediv" title="<?php echo __( 'Click to toggle', 'woocommerce-boost-sales' ); ?>"></div>
		<strong class="item-title"><?php echo $title . ' (#' . $product_id . ')' . $parent ?></strong>
	</h3>
	<div class="wbs-wcpb-bundled-item-data wc-metabox-content">
		<div class="wbs-wcpb-bundled-item-data-content">
			<input type="hidden" name="_wbs_wcpb_bundle_data[<?php echo $metabox_id; ?>][bundle_order]" class="wbs-wcpb-bundled-item-position" value="<?php echo $metabox_id; ?>" />
			<input type="hidden" name="_wbs_wcpb_bundle_data[<?php echo $metabox_id; ?>][product_id]" class="wbs-wcpb-product-id" value="<?php echo $product_id; ?>" />
			<?php do_action( 'wbs_wcpb_admin_product_bundle_data', $metabox_id, $product_id, $item_data, $post_id ); ?>
		</div>
	</div>
</div>
