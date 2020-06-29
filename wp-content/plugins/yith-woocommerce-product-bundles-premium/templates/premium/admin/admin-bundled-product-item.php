<?php
/**
 * Admin Add Bundled Product markup.
 *
 * @version 4.8.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

$debug = !empty( $_REQUEST[ 'yith_wcpb_debug' ] );

$item_data   = !empty( $item_data ) ? $item_data : array();
$open_closed    = !empty( $_POST[ 'open_closed' ] ) ? $_POST[ 'open_closed' ] : 'closed';
$content_hidden = 'closed' === $open_closed ? 'hidden' : '';

$edit_link  = get_edit_post_link( $product_id );
$item_title = "<a class='yith-wcbep-edit-product-btn dashicons dashicons-admin-generic' target='_blank' href='{$edit_link}'></a>{$title} &ndash; #{$product_id}";

$product        = wc_get_product( $product_id );
if ( !$product )
    $is_purchasable = false;
else if ( $product->is_type( 'variable' ) )
    $is_purchasable = true;
else
    $is_purchasable = $product->is_purchasable();
?>
<div class="yith-wcpb-bundled-item wc-metabox <?php echo $open_closed ?>" rel="<?php echo $metabox_id; ?>">
    <h3>
        <button type="button" class="yith-wcpb-remove-bundled-product-item button"><?php echo __( 'Remove', 'woocommerce' ); ?></button>
        <div class="handlediv" title="<?php echo __( 'Click to toggle', 'woocommerce' ); ?>"></div>
        <strong class="item-title"><?php echo $item_title ?></strong>
        <?php if ( !$is_purchasable ): ?>
            <span class="yith-wcpb-bundled-items-info not-purchasable">
                <?php _e( 'Not Purchasable', 'yith-woocommerce-product-bundles' ) ?>
            </span>
        <?php endif; ?>
        <?php
        if ( $debug ) {
            $debug_data = array(
                'product_type'   => $product->get_type(),
                'product_exists' => $product->exists(),
                'product_status' => $product->get_status(),
                'user_can_edit'  => current_user_can( 'edit_post', $product->get_id() ),
                'get_price'      => $product->get_price(),
            );

            foreach ( $debug_data as $debug_data_key => $debug_data_value ) {
                echo "<span class='yith-wcpb-bundled-items-info debug'><h4>$debug_data_key</h4><pre>";
                var_dump( $debug_data_value );
                echo '</pre></span>';
            }
        }
        ?>
    </h3>
    <div class="yith-wcpb-bundled-item-data wc-metabox-content <?php echo $content_hidden ?>">
        <div class="yith-wcpb-bundled-item-data-content">
            <input type="hidden" name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][bundle_order]" class="yith-wcpb-bundled-item-position" value="<?php echo $metabox_id; ?>"/>
            <input type="hidden" name="_yith_wcpb_bundle_data[<?php echo $metabox_id; ?>][product_id]" class="yith-wcpb-product-id" value="<?php echo $product_id; ?>"/>
            <?php do_action( 'yith_wcpb_admin_product_bundle_data', $metabox_id, $product_id, $item_data, $post_id ); ?>
        </div>
    </div>
</div>
