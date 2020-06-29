<?php

if (! defined('ABSPATH') )
    exit;

function wcms_get_cheapest_shipping_rate( $rates = array() ) {
    $cheapest   = false;
    $last_cost  = false;

    foreach ( $rates as $rate ) {
        if ( $last_cost === false ) {
            $last_cost  = $rate->cost;
            $cheapest   = $rate;
            continue;
        }

        if ( $rate->cost < $last_cost ) {
            $last_cost  = $rate->cost;
            $cheapest   = $rate;
        }
    }

    if ( $cheapest ) {
        $cheapest = (array)$cheapest;
    }

    return $cheapest;
}

function wcms_get_address( $address ) {
    foreach ( $address as $key => $value ) {
        if ( strpos( $key, 'shipping_' ) === false ) {
            $address[ 'shipping_'. $key ] = $value;
        }

        $addr_key = str_replace( 'shipping_', '', $key );
        $address[ $addr_key ] = $value;
    }

    return $address;
}

function wcms_get_formatted_address( $address ) {
    $address = wcms_get_address( $address );
    return apply_filters( 'wc_ms_formatted_address', WC()->countries->get_formatted_address( $address ), $address );
}

function wcms_count_real_cart_items() {

    $count = 0;

    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

        if ( !$cart_item['data']->needs_shipping() )
            continue;

        if ( isset($cart_item['bundled_by']) && !empty($cart_item['bundled_by']) )
            continue;

        if ( isset($cart_item['composite_parent']) && !empty($cart_item['composite_parent']) )
            continue;

        $count++;
    }

    return $count;
}

function wcms_get_real_cart_items() {

    $items = array();

    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

        if ( !$cart_item['data']->needs_shipping() )
            continue;

        if ( isset($cart_item['bundled_by']) && !empty($cart_item['bundled_by']) )
            continue;

        if ( isset($cart_item['composite_parent']) && !empty($cart_item['composite_parent']) )
            continue;

        $items[$cart_item_key] = $cart_item;
    }

    return $items;
}

function wcms_get_product( $product_id ) {
    if ( function_exists( 'get_product' ) ) {
        return get_product( $product_id );
    } else {
        return new WC_Product( $product_id );
    }
}

function wcms_session_get( $name ) {

    if ( isset( WC()->session ) ) {
        // WC 2.0
        if ( isset( WC()->session->$name ) ) return WC()->session->$name;
    } else {
        // old style
        if ( isset( $_SESSION[ $name ] ) ) return $_SESSION[ $name ];
    }

    return null;
}

function wcms_session_isset( $name ) {

    if ( isset(WC()->session) ) {
        // WC 2.0
        return (isset( WC()->session->$name ));
    } else {
        return (isset( $_SESSION[$name] ));
    }
}

function wcms_session_set( $name, $value ) {

    if ( isset( WC()->session ) ) {
        // WC 2.0
        unset( WC()->session->$name );
        WC()->session->$name = $value;
    } else {
        // old style
        $_SESSION[ $name ] = $value;
    }
}

function wcms_session_delete( $name ) {

    if ( isset( WC()->session ) ) {
        // WC 2.0
        unset( WC()->session->$name );
    } else {
        // old style
        unset( $_SESSION[ $name ] );
    }
}
