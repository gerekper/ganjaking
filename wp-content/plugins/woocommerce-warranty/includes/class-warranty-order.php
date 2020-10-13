<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Warranty_Order {

    public function __construct() {
        // Initialize order meta. Need to be called after plugins_loaded because of WC_VERSION check.
        add_action( 'plugins_loaded', array( $this, 'init_order_meta' ), 20 );
    }

    /**
     * Actions for order item meta.
     *
     * @since 1.8.6
     * @return void
     */
    public function init_order_meta() {
        if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
            add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta_bwc' ), 10, 3 );

            // order status changed
            add_action( 'woocommerce_order_status_changed', array($this, 'order_status_changed_bwc'), 10, 3 );
        } else {
            add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_item_meta' ), 10, 3 );

            // order status changed
            add_action( 'woocommerce_order_status_changed', array($this, 'order_status_changed'), 10, 4 );
        }
    }

    /**
     * Listens to order status changes and sets the completed date if the current
     * order status matches the start status of the warranty period
     *
     * @param int       $order_id
     * @param string    $old_status
     * @param string    $new_status
     */
    public function order_status_changed_bwc( $order_id, $old_status, $new_status ) {
        // update order's date of completion
        $handler = function ( $order ) {
            update_post_meta( $order->id, '_completed_date', current_time( 'mysql' ) );
        };

        $this->handle_status_change( $order_id, $new_status, $handler );
    }

    /**
     * Listens to order status changes and sets the completed date if the current
     * order status matches the start status of the warranty period
     *
     * @param int       $order_id
     * @param string    $old_status
     * @param string    $new_status
     * @param WC_Order  Actual order
     */
    public function order_status_changed( $order_id, $old_status, $new_status, $order ) {
        // update order's date of completion
        $handler = function () use ( $order ) {
            $order->set_date_completed( current_time( 'mysql' ) );
            $order->save();
        };

        $this->handle_status_change( $order_id, $new_status, $handler );
    }

    /**
     * Handler for the order status change.
     *
     * @param int       $order_id
     * @param string    $new_status
     * @param callable  $handler
     */
    private function handle_status_change( $order_id, $new_status, $handler ) {
        global $woocommerce;

        $order          = wc_get_order( $order_id );

        if ( 'completed' !== $new_status ) {
            return;
        }

        $items          = $order->get_items();
        $has_warranty   = false;

        foreach ( $items as $item ) {
            $warranty       = false;
            $addon_index    = false;
            $metas          = (isset($item['item_meta'])) ? $item['item_meta'] : array();

            foreach ( $metas as $key => $value ) {
                $value = version_compare( WC_VERSION, '3.0', '<' ) ? $value[0] : $value;

                if ( $key == '_item_warranty' ) {
                    $warranty = maybe_unserialize( $value );
                }
            }

            if ( $warranty ) {
                // update order's date of completion
                $handler( $order );

                break; // only need to update once per order
            }
        }
    }

    /**
     * Include add-ons line item meta.
     *
     * @param  WC_Order_Item_Product $item          Order item data.
     * @param  string                $cart_item_key Cart item key.
     * @param  array                 $values        Order item values.
     */
    public function order_item_meta( $item, $cart_item_key, $values ) {
        $_product       = $values['data'];
        $_prod_id       = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $_product->variation_id ) ) ? $_product->variation_id : $_product->get_id();
        $warranty       = warranty_get_product_warranty( $_prod_id );
        $warranty_label = $warranty['label'];

        if ( $warranty ) {
            if ( $warranty['type'] == 'addon_warranty' ) {
                $warranty_index = isset($values['warranty_index']) ? $values['warranty_index'] : false;

                $item->update_meta_data( '_item_warranty_selected', $warranty_index );
            }

            if ( 'no_warranty' !== $warranty['type'] ) {
                $item->update_meta_data( '_item_warranty', $warranty );
            }
        }
    }

    /**
     * Adds the warranty to the item as item meta
     *
     * @access public
     * @param mixed $item_id
     * @param mixed $values
     * @return void
     */
    function order_item_meta_bwc( $item_id, $values ) {
        $_product       = $values['data'];
        $_prod_id       = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $_product->variation_id ) ) ? $_product->variation_id : $_product->get_id();
        $warranty       = warranty_get_product_warranty( $_prod_id );
        $warranty_label = $warranty['label'];

        if ( $warranty ) {
            if ( $warranty['type'] == 'addon_warranty' ) {
                $warranty_index = isset($values['warranty_index']) ? $values['warranty_index'] : false;

                wc_add_order_item_meta( $item_id, '_item_warranty_selected', $warranty_index );
            }

            wc_add_order_item_meta( $item_id, '_item_warranty', $warranty );
        }
    }

    /**
     * Check if an order contain items that have valid warranties
     *
     * @param WC_Order $order
     * @return bool
     */
    public static function order_has_warranty( $order ) {
        global $woocommerce;

        $items          = $order->get_items();
        $has_warranty   = false;
        $warranty       = false;
        $addon_index    = null;

        foreach ( $items as $item ) {
            $metas = (isset($item['item_meta'])) ? $item['item_meta'] : array();

            foreach ( $metas as $key => $value ) {
                $value = version_compare( WC_VERSION, '3.0', '<' ) ? $value[0] : $value;

                if ( $key == '_item_warranty' ) {
                    $warranty = maybe_unserialize( $value );
                } elseif ( $key == '_item_warranty_selected' ) {
                    $addon_index = $value;
                }
            }

            if ( $warranty !== false ) {
                // order's date of completion must be within the warranty period
                if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
                    $completed = get_post_meta( $order->id, '_completed_date', true);
                } else {
                    $completed = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;
                }

                if ( $warranty['type'] == 'addon_warranty' ) {
                    $valid_until    = false;
                    $addon          = (isset($warranty['addons'][$addon_index])) ? $warranty['addons'][$addon_index] : null;

                    if (! $addon ) {
                        continue;
                    }

                    if (! empty($completed) ) {
                        $valid_until = strtotime( $completed .' +'. $addon['value'] .' '. $addon['duration'] );
                    }

                    if ( $valid_until && current_time('timestamp') < $valid_until ) {
                        $has_warranty = true;
                        break;
                    }
                } elseif ( $warranty['type'] == 'included_warranty' ) {
                    if ( $warranty['length'] == 'lifetime' ) {
                        $has_warranty = true;
                        break;
                    } else {
                        // order's date of completion must be within the warranty period
                        $valid_until    = false;

                        if (! empty($completed) ) {
                            $valid_until = strtotime( $completed .' +'. $warranty['value'] .' '. $warranty['duration'] );
                        }

                        if ( $valid_until && current_time('timestamp') < $valid_until ) {
                            $has_warranty = true;
                            break;
                        }
                    }
                }
            }
        }

        if (! $has_warranty ) {
            $query_args     = array(
                'post_type'         => 'warranty_request',
                'orderby'           => 'date',
                'order'             => 'DESC'
            );

            $query_args['meta_query'][] = array(
                'key'       => '_order_id',
                'value'     => WC_Warranty_Compatibility::get_order_prop( $order, 'id' ),
                'compare'   => '='
            );

            $wp_query = new WP_Query();
            $wp_query->query($query_args);

            $total_items = $wp_query->found_posts;
            wp_reset_postdata();

            if ( $total_items > 0 ) {
                $has_warranty = true;
            }
        }

        return apply_filters( 'order_has_warranty', $has_warranty, $order );
    }

}

new Warranty_Order();
