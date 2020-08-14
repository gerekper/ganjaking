<?php
/**
 * Admin Bundle Options TAB
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

global $post;

$bundle_data = get_post_meta( $post->ID, '_yith_wcpb_bundle_data', true );

?>
<div id="yith_bundled_product_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper">

    <div class="options_group yith-wcpb-bundle-metaboxes-wrapper">

        <div id="yith-wcpb-bundle-metaboxes-wrapper-inner">

            <p class="toolbar">
                <a href="#" class="close_all"><?php _e( 'Close all', 'woocommerce' ); ?></a>
                <a href="#" class="expand_all"><?php _e( 'Expand all', 'woocommerce' ); ?></a>
            </p>

            <div class="yith-wcpb-bundled-items wc-metaboxes ui-sortable">
                <?php
                if ( !empty( $bundle_data ) ) {
                    $i = 0;
                    foreach ( $bundle_data as $item_id => $item_data ) {
                        //$metabox_id     = $item_data[ 'bundle_order' ];
                        $i++;
                        $metabox_id = $i;
                        $post_id    = $post->ID;
                        $product_id = $item_data[ 'product_id' ];

                        $title       = get_the_title( $product_id );
                        $open_closed = 'closed';
                        ob_start();
                        include YITH_WCPB_TEMPLATE_PATH . '/admin/admin-bundled-product-item.php';
                        echo ob_get_clean();
                    }
                }
                ?>
            </div>
            <p class="yith-wcpb-bundled-prod-toolbar toolbar">
                            <span class="yith-wcpb-bundled-prod-toolbar-wrapper">
                                <button type="button" id="yith-wcpb-add-bundled-product"
                                        class="button button-primary"><?php _e( 'Add Product', 'yith-woocommerce-product-bundles' ); ?></button>
                            </span>
            </p>
        </div>
    </div>
</div>