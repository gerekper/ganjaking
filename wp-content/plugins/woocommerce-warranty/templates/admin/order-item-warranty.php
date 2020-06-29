<?php
$name = $value = $expiry = false;

$order = wc_get_order( $order_id );

if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
    $order_date = get_post_meta( $order->id, '_completed_date', true);
} else {
    $order_date = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;
}

if ( empty( $warranty['label'] ) ) {
    $product_warranty = warranty_get_product_warranty( $item['product_id'] );
    $warranty['label'] = $product_warranty['label'];
}

if ( $warranty['type'] == 'addon_warranty' ) {
    $addons = $warranty['addons'];

    $warranty_index = wc_get_order_item_meta( $item_id, '_item_warranty_selected', true );

    if ( $warranty_index !== false && isset($addons[$warranty_index]) && !empty($addons[$warranty_index]) ) {
        $addon  = $addons[$warranty_index];
        $name   = $warranty['label'];
        $value  = $GLOBALS['wc_warranty']->get_warranty_string( $addon['value'], $addon['duration'] );

        if ( $order_date ) {
            $expiry = warranty_get_date( $order_date, $addon['value'], $addon['duration'] );
        }
    }
} elseif ( $warranty['type'] == 'included_warranty' ) {
    if ( $warranty['length'] == 'limited' ) {
        $name   = $warranty['label'];
        $value  = $GLOBALS['wc_warranty']->get_warranty_string( $warranty['value'], $warranty['duration'] );

        if ( $order_date ) {
            $expiry = warranty_get_date( $order_date, $warranty['value'], $warranty['duration'] );
        }

    }
}

if ( !$name || ! $value ) {
    return;
}

?>
<table cellspacing="0" class="display_meta">
    <tr>
        <th><?php echo wp_kses_post( $name ); ?>:</th>
        <td><?php
            echo wp_kses_post( $value );

            if ( $expiry ) {
                if ( current_time('timestamp') > strtotime( $expiry ) ) {
                    echo ' <small>(expired on '. $expiry .')</small>';
                } else {
                    echo ' <small>(expires '. $expiry .')</small>';
                }
            }

        ?></td>
    </tr>
</table>
