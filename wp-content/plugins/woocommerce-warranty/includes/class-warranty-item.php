<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Warranty_Item {

    public $item_id;
    public $type;
    public $label;
    public $addons;
    public $addon_selected;
    public $addon_default;
    public $length;
    public $duration_value;
    public $duration_type;
    public $no_warranty_option;
    public $order_id;

    /**
     * Initialize the object
     * @param int $item_id The WooCommerce Item ID
     */
    public function __construct( $item_id ) {
        $warranty   = wc_get_order_item_meta( $item_id, '_item_warranty', true );
        $selected   = wc_get_order_item_meta( $item_id, '_item_warranty_selected', true );

        $this->item_id = $item_id;
        $this->addon_selected = ( $selected ) ? $selected : false;

        if ( !$warranty ) {
            $this->type = 'no_warranty';
            return;
        }

        foreach ( $warranty as $key => $value ) {
            switch ( $key ) {
                case 'value':
                    $this->duration_value = $value;
                    break;

                case 'duration':
                    $this->duration_type = $value;
                    break;

                case 'default':
                    $this->addon_default = $value;
                    break;

                default:
                    $this->$key = $value;
                    break;
            }
        }
    }

    public function get_order_id() {
        global $wpdb;

        if ( !$this->order_id ) {
            $this->order_id = $wpdb->get_var($wpdb->prepare(
                "SELECT order_id
                FROM {$wpdb->prefix}woocommerce_order_items
                WHERE order_item_id = %d",
                $this->item_id
            ));
        }

        return $this->order_id;
    }

    /**
     * Get the available number of RMAs for the current order item.
     *
     * The available number is determined by the quantity of order item minus
     * the number of warranty requests made against the same order item.
     *
     * @return int
     */
    public function get_quantity_remaining() {
        global $wpdb;

        $row = $wpdb->get_row( $wpdb->prepare(
            "SELECT *
            FROM {$wpdb->prefix}wc_warranty_products
            WHERE order_item_index = %d",
            $this->item_id
        ) );

        $qty            = wc_get_order_item_meta( $this->item_id, '_qty', true );;
        $product_id     = wc_get_order_item_meta( $this->item_id, '_product_id', true );
        $variation_id   = wc_get_order_item_meta( $this->item_id, '_variation_id', true );

        if ( $variation_id ) {
            $product_id = $variation_id;
        }

        $requests = warranty_search( $this->get_order_id(), $product_id, $this->item_id );

        if ( $requests ) {
            $used = 0;
            foreach ( $requests as $request ) {
                $request = warranty_load( $request->ID );

                foreach ( $request['products'] as $product ) {
                    if ( $product['order_item_index'] == $this->item_id ) {
                        $used += $product['quantity'];
                    }
                }
            }

            $qty -= $used;
        }

        return $qty;
    }

    /**
     * Check if the item can send a warranty request
     *
     * This will return true for the following scenarios:
     *  - Type is 'included' and duration is 'lifetime' and remaining quantity > 0
     *  - Type is 'included' or 'addon' and expiry is in a future date and remainig quantity > 0
     *
     * @return bool
     */
    public function has_warranty() {
        $has_warranty   = false;
        $remaining      = $this->get_quantity_remaining();


        if ( $remaining < 1 ) {
            return $has_warranty;
        }

        if ( $this->type == 'included_warranty' ) {
            if ( 'lifetime' === $this->length ) {
                $has_warranty = true;
            } else {
                $now    = current_time( 'timestamp' );
                $expiry = $this->get_expiry();

                if ( !$expiry || $now < $expiry ) {
                    $has_warranty = true;
                }
            }
        } elseif ( $this->type == 'addon_warranty' ) {
	        if ( isset( $this->addons[ $this->addon_selected ] ) ) {
		        $addon  = $this->addons[ $this->addon_selected ];
		        $now    = current_time( 'timestamp' );
		        $expiry = $this->get_expiry( $addon['value'], $addon['duration'] );

		        if ( !$expiry || $now < $expiry ) {
			        $has_warranty = true;
		        }
	        }
        }

        return $has_warranty;
    }

    /**
     * Get the warranty's expiration date.
     *
     * @param string $duration_value
     * @param string $duration_type
     *
     * @return bool|int
     */
    public function get_expiry( $duration_value = '', $duration_type = '' ) {
        $expiry         = false;
        $order          = wc_get_order( $this->get_order_id() );

        if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
            $completed_date = get_post_meta( $order->id, '_completed_date', true);
        } else {
            $completed_date = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;
        }

        if ( empty( $duration_value ) ) {
            $duration_value = $this->duration_value;
        }

        if ( empty( $duration_type ) ) {
            $duration_type = $this->duration_type;
        }

        if ( $completed_date ) {
            $expiry = strtotime( $completed_date . ' +'. $duration_value .' '. $duration_type );
        }

        return $expiry;
    }

    public function is_expired() {}



}
