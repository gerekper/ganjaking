<?php
global $wpdb;

set_time_limit(0);

// Rename the warranty_shipping_label meta key
$wpdb->query("UPDATE {$wpdb->postmeta} SET meta_key = '_warranty_shipping_label' WHERE meta_key = 'warranty_shipping_label'");

// Remove warranty order item meta
$items = $wpdb->get_results("SELECT order_item_id, meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = '_item_warranty'");

foreach ( $items as $item ) {
    $warranty   = maybe_unserialize( $item->meta_value );
    $value      = false;

    if ( !is_array( $warranty ) || !isset( $warranty['type'] ) ) {
        continue;
    }

    if ( $warranty['type'] == 'addon_warranty' ) {
        $addons = $warranty['addons'];

        $warranty_index = isset($values['warranty_index']) ? $values['warranty_index'] : false;

        if ( $warranty_index !== false && isset($addons[$warranty_index]) && !empty($addons[$warranty_index]) ) {
            $addon  = $addons[$warranty_index];
            $value  = $GLOBALS['wc_warranty']->get_warranty_string( $addon['value'], $addon['duration'] );

            if ( $addon['amount'] > 0 ) {
                $value .= ' (' . strip_tags(wc_price( $addon['amount'] )) . ')';
            }
        }
    } elseif ( $warranty['type'] == 'included_warranty' ) {
        if ( $warranty['length'] == 'lifetime' ) {
            $value  = __('Lifetime', 'wc_warranty');
        } elseif ( $warranty['length'] == 'limited' ) {
            $value  = $GLOBALS['wc_warranty']->get_warranty_string( $warranty['value'], $warranty['duration'] );
        }
    }

    if ( !$value ) {
        continue;
    }

    $wpdb->query("DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = {$item->order_item_id} AND meta_value = '$value'");
}