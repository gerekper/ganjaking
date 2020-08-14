<?php
/**
 * Frontend class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Bundles
 * @version 1.1.1
 */

if ( !defined( 'YITH_WCPB' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCPB_Frontend' ) ) {
    /**
     * Frontend class.
     * The class manage all the Frontend behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCPB_Frontend {

        /**
         * Single instance of the class
         *
         * @var YITH_WCPB_Frontend
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Plugin version
         *
         * @var string
         * @since 1.0.0
         */
        public $version = YITH_WCPB_VERSION;

        public $this_is_product = null;

        public $templates = array();

        /**
         * @return YITH_WCPB_Frontend|YITH_WCPB_Frontend_Premium
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct() {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            // C A R T
            add_action( 'woocommerce_yith_bundle_add_to_cart', array( $this, 'woocommerce_yith_bundle_add_to_cart' ) );
            add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'woocommerce_add_to_cart_validation' ), 10, 6 );

            add_filter( 'woocommerce_add_cart_item_data', array( $this, 'woocommerce_add_cart_item_data' ), 10, 2 );
            add_action( 'woocommerce_add_to_cart', array( $this, 'woocommerce_add_to_cart' ), 10, 6 );
            add_filter( 'woocommerce_cart_item_remove_link', array( $this,
                                                                    'woocommerce_cart_item_remove_link' ), 10, 2 );
            add_filter( 'woocommerce_cart_item_quantity', array( $this, 'woocommerce_cart_item_quantity' ), 10, 2 );
            add_action( 'woocommerce_after_cart_item_quantity_update', array( $this,
                                                                              'update_cart_item_quantity' ), 1, 2 );
            add_action( 'woocommerce_before_cart_item_quantity_zero', array( $this, 'update_cart_item_quantity' ), 1 );

            add_filter( 'woocommerce_cart_item_price', array( $this, 'woocommerce_cart_item_price' ), 99, 3 );
            add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'bundles_item_subtotal' ), 99, 3 );
            add_filter( 'woocommerce_checkout_item_subtotal', array( $this, 'bundles_item_subtotal' ), 10, 3 );
            add_filter( 'woocommerce_add_cart_item', array( $this, 'woocommerce_add_cart_item' ), 10, 2 );
            add_action( 'woocommerce_cart_item_removed', array( $this, 'woocommerce_cart_item_removed' ), 10, 2 );
            add_action( 'woocommerce_cart_item_restored', array( $this, 'woocommerce_cart_item_restored' ), 10, 2 );

            add_filter( 'woocommerce_cart_contents_count', array( $this, 'woocommerce_cart_contents_count' ) );

            add_filter( 'woocommerce_cart_item_class', array( $this, 'table_item_class_bundle' ), 10, 2 );

            add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'woocommerce_get_cart_item_from_session' ), 10, 3 );
            add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'remove_bundled_items_without_parent_bundle' ), 99 );

            // O R D E R
            add_filter( 'woocommerce_order_formatted_line_subtotal', array( $this, 'woocommerce_order_formatted_line_subtotal' ), 10, 3 );
            add_filter( 'woocommerce_checkout_create_order_line_item', array( $this, 'woocommerce_checkout_create_order_line_item' ), 10, 4 );
            add_filter( 'woocommerce_order_item_class', array( $this, 'table_item_class_bundle' ), 10, 2 );

            add_filter( 'woocommerce_order_item_needs_processing', array( $this, 'woocommerce_order_item_needs_processing' ), 10, 3 );

            /**
             * Order Again
             *
             * @since 1.2.11
             */
            add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'woocommerce_order_again_cart_item_data' ), 10, 2 );
            add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'woocommerce_order_again_add_to_cart_validation' ), 10, 6 );

            // S H I P P I N G
            add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'woocommerce_cart_shipping_packages' ), 99 );
        }

        /**
         * remove bundled items in cart if the bundle is not in cart
         * (added to fix an issue when removing the bundle if YITH Dynamic Pricing is active)
         *
         * @since 1.2.18 Premium
         */
        public function remove_bundled_items_without_parent_bundle() {
            if ( empty( WC()->cart->cart_contents ) ) {
                return;
            }
            foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
                if ( isset( $cart_item[ 'bundled_by' ] ) ) {
                    $bundle_key = $cart_item[ 'bundled_by' ];
                    if ( !isset( WC()->cart->cart_contents[ $bundle_key ] ) ) {
                        WC()->cart->remove_cart_item( $cart_item_key );
                    }
                }
            }
        }


        /**
         * add cart item data for bundles when 'Order Again'
         *
         * @param array         $cart_item_data
         * @param WC_Order_Item $item
         * @since 1.2.11
         * @return array
         */
        public function woocommerce_order_again_cart_item_data( $cart_item_data, $item ) {
            if ( $item instanceof WC_Order_Item_Product ) {
                $product = $item->get_product();
                if ( $product && $product->is_type( 'yith_bundle' ) ) {
                    // I'm a bundle
                    $cartstamp = $item->get_meta( '_cartstamp' );

                    if ( $cartstamp ) {
                        $cart_item_data[ 'cartstamp' ]     = $cartstamp;
                        $cart_item_data[ 'bundled_items' ] = array();
                    }
                } else {
                    // Maybe bundled items
                    $_bundled_by = $item->get_meta( '_bundled_by' );

                    if ( $_bundled_by ) {
                        $cart_item_data[ 'yith_wcpb_order_again_bundled_item_to_remove' ] = true;
                    }
                }
            }

            return $cart_item_data;
        }

        /**
         * remove bundled items in cart when 'Order Again' to prevent duplicates
         *
         * @param bool  $validation
         * @param int   $product_id
         * @param int   $quantity
         * @param int   $variation_id
         * @param array $variations
         * @param array $cart_item_data
         * @since 1.2.11
         * @return bool
         */
        public function woocommerce_order_again_add_to_cart_validation( $validation, $product_id = '', $quantity = 1, $variation_id = '', $variations = array(), $cart_item_data = array() ) {
            return empty( $cart_item_data[ 'yith_wcpb_order_again_bundled_item_to_remove' ] ) && $validation;
        }

        /**
         * Edit the count of cart contents
         * exclude bundled items from the count
         *
         * @param $count
         * @return int
         */
        public function woocommerce_cart_contents_count( $count ) {
            $cart_contents = WC()->cart->cart_contents;

            $bundled_items_count = 0;
            foreach ( $cart_contents as $cart_item_key => $cart_item ) {
                if ( !empty( $cart_item[ 'bundled_by' ] ) ) {
                    $bundled_items_count += $cart_item[ 'quantity' ];
                }
            }

            return intval( $count - $bundled_items_count );
        }


        /**
         * add CSS class in table items in checkout and order
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function table_item_class_bundle( $classname, $cart_item ) {
            if ( isset( $cart_item[ 'bundled_by' ] ) )
                return $classname . ' yith-wcpb-child-of-bundle-table-item'; elseif ( isset( $cart_item[ 'cartstamp' ] ) )
                return $classname . ' yith-wcpb-bundle-table-item';

            return $classname;
        }

        /**
         * create item data [create the cartstamp if not exist]
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_add_cart_item_data( $cart_item_data, $product_id ) {
            $product = wc_get_product( $product_id );
            if ( !$product || !$product->is_type( 'yith_bundle' ) )
                return $cart_item_data;

            /** @var WC_Product_Yith_Bundle $product */

            if ( isset( $cart_item_data[ 'cartstamp' ] ) && isset( $cart_item_data[ 'bundled_items' ] ) ) {
                return $cart_item_data;
            }

            $bundled_items = $product->get_bundled_items();
            if ( !!$bundled_items ) {
                $cartstamp = array();

                foreach ( $bundled_items as $bundled_item_id => $bundled_item ) {

                    $id                   = $bundled_item->product_id;
                    $bundled_product_type = $bundled_item->product->get_type();

                    $bundled_product_quantity = isset ( $_REQUEST[ apply_filters( 'woocommerce_product_yith_bundle_field_prefix', '', $product_id ) . 'yith_bundle_quantity_' . $bundled_item_id ] ) ? absint( $_REQUEST[ apply_filters( 'woocommerce_product_yith_bundle_field_prefix', '', $product_id ) . 'yith_bundle_quantity_' . $bundled_item_id ] ) : $bundled_item->get_quantity();

                    $cartstamp[ $bundled_item_id ][ 'product_id' ] = $id;
                    $cartstamp[ $bundled_item_id ][ 'type' ]       = $bundled_product_type;
                    $cartstamp[ $bundled_item_id ][ 'quantity' ]   = $bundled_product_quantity;
                    $cartstamp[ $bundled_item_id ]                 = apply_filters( 'woocommerce_yith_bundled_item_cart_item_identifier', $cartstamp[ $bundled_item_id ], $bundled_item_id );
                }

                $cart_item_data[ 'cartstamp' ]     = $cartstamp;
                $cart_item_data[ 'bundled_items' ] = array();
            }

            return $cart_item_data;
        }

        /**
         * Add to cart for Product Bundle
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
            if ( isset( $cart_item_data[ 'cartstamp' ] ) && !isset( $cart_item_data[ 'bundled_by' ] ) ) {
                $bundled_items_cart_data = array( 'bundled_by' => $cart_item_key );

                foreach ( $cart_item_data[ 'cartstamp' ] as $bundled_item_id => $bundled_item_stamp ) {
                    $bundled_item_cart_data                      = $bundled_items_cart_data;
                    $bundled_item_cart_data[ 'bundled_item_id' ] = $bundled_item_id;

                    $item_quantity = $bundled_item_stamp[ 'quantity' ];
                    $i_quantity    = $item_quantity * $quantity;
                    $prod_id       = $bundled_item_stamp[ 'product_id' ];

                    $bundled_item_cart_key = $this->bundled_add_to_cart( $product_id, $prod_id, $i_quantity, $variation_id, '', $bundled_item_cart_data );

                    if ( $bundled_item_cart_key && !in_array( $bundled_item_cart_key, WC()->cart->cart_contents[ $cart_item_key ][ 'bundled_items' ] ) ) {
                        WC()->cart->cart_contents[ $cart_item_key ][ 'bundled_items' ][] = $bundled_item_cart_key;
                        WC()->cart->cart_contents[ $cart_item_key ][ 'yith_parent' ]     = $cart_item_key;
                    }
                }
            }
        }

        /**
         * add single bundled item to cart
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function bundled_add_to_cart( $bundle_id, $product_id, $quantity = 1, $variation_id = '', $variation = '', $cart_item_data ) {
            if ( $quantity <= 0 )
                return false;

            $cart_item_data = ( array ) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity );
            $cart_id        = WC()->cart->generate_cart_id( $product_id, $variation_id, $variation, $cart_item_data );
            $cart_item_key  = WC()->cart->find_product_in_cart( $cart_id );

            if ( 'product_variation' == get_post_type( $product_id ) ) {
                $variation_id = $product_id;
                $product_id   = wp_get_post_parent_id( $variation_id );
            }

            $product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
            yit_set_prop( $product_data, 'yith_wcpb_is_bundled', true );

            if ( !$cart_item_key ) {
                $cart_item_key                              = $cart_id;
                WC()->cart->cart_contents[ $cart_item_key ] = apply_filters( 'woocommerce_add_cart_item', array_merge( $cart_item_data, array(
                    'product_id'   => $product_id,
                    'variation_id' => $variation_id,
                    'variation'    => $variation,
                    'quantity'     => $quantity,
                    'data'         => $product_data
                ) ), $cart_item_key );
            }

            return $cart_item_key;
        }

        /**
         * remove 'remove link' for bundled product in cart
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_cart_item_remove_link( $link, $cart_item_key ) {
            if ( isset( WC()->cart->cart_contents[ $cart_item_key ][ 'bundled_by' ] ) ) {
                $bundle_cart_key = WC()->cart->cart_contents[ $cart_item_key ][ 'bundled_by' ];
                if ( isset( WC()->cart->cart_contents[ $bundle_cart_key ] ) ) {
                    return '';
                }
            }

            return $link;
        }

        /**
         * cart item quantity
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_cart_item_quantity( $quantity, $cart_item_key ) {
            if ( isset( WC()->cart->cart_contents[ $cart_item_key ][ 'bundled_by' ] ) ) {
                return WC()->cart->cart_contents[ $cart_item_key ][ 'quantity' ];
            }

            return $quantity;
        }

        /**
         * update cart item quantity
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function update_cart_item_quantity( $cart_item_key, $quantity = 0 ) {
            if ( !empty( WC()->cart->cart_contents[ $cart_item_key ] ) ) {

                if ( $quantity <= 0 ) {
                    $quantity = 0;
                } else {
                    $quantity = WC()->cart->cart_contents[ $cart_item_key ][ 'quantity' ];
                }

                if ( !empty( WC()->cart->cart_contents[ $cart_item_key ][ 'cartstamp' ] ) && !isset( WC()->cart->cart_contents[ $cart_item_key ][ 'bundled_by' ] ) ) {
                    $stamp = WC()->cart->cart_contents[ $cart_item_key ][ 'cartstamp' ];
                    foreach ( WC()->cart->cart_contents as $key => $value ) {
                        if ( isset( $value[ 'bundled_by' ] ) && $cart_item_key == $value[ 'bundled_by' ] ) {
                            $bundle_item_id  = $value[ 'bundled_item_id' ];
                            $bundle_quantity = $stamp[ $bundle_item_id ][ 'quantity' ];
                            WC()->cart->set_quantity( $key, $quantity * $bundle_quantity, false );
                        }
                    }
                }
            }
        }

        /**
         * remove cart item price for bundled product
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_cart_item_price( $price, $cart_item, $cart_item_key ) {
            if ( isset( $cart_item[ 'bundled_by' ] ) ) {
                $bundle_cart_key = $cart_item[ 'bundled_by' ];
                if ( isset( WC()->cart->cart_contents[ $bundle_cart_key ] ) ) {
                    return '';
                }
            }

            return $price;
        }

        /**
         * remove cart item subtotal for bundled product
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function bundles_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
            if ( isset( $cart_item[ 'bundled_by' ] ) ) {
                $bundle_cart_key = $cart_item[ 'bundled_by' ];
                if ( isset( WC()->cart->cart_contents[ $bundle_cart_key ] ) ) {
                    return '';
                }
            }
            if ( isset( $cart_item[ 'bundled_items' ] ) ) {
                if ( $cart_item[ 'data' ]->get_price() == 0 ) {
                    return '';
                }
            }

            return $subtotal;
        }

        /**
         * get template for Bundle Product add to cart
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_yith_bundle_add_to_cart() {
            /** @var WC_Product_Yith_Bundle $product */
            global $product;
            $bundled_items = $product->get_bundled_items();
            if ( $bundled_items ) {
                wc_get_template( 'single-product/add-to-cart/yith-bundle.php', array(), '', YITH_WCPB_TEMPLATE_PATH . '/' );
            }
        }

        /**
         * woocommerce Validation Bundle Product for add to cart
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_add_to_cart_validation( $add_flag, $product_id, $product_quantity, $variation_id = '', $variations = array(), $cart_item_data = array() ) {
            /** @var WC_Product_Yith_Bundle $product */
            $product = wc_get_product( $product_id );

            if ( $product->is_type( 'yith_bundle' ) && get_option( 'woocommerce_manage_stock' ) == 'yes' ) {
                $bundled_items = $product->get_bundled_items();
                foreach ( $bundled_items as $bundled_item ) {
                    /** @var YITH_WC_Bundled_Item $bundled_item */
                    $bundled_prod = $bundled_item->get_product();
                    if ( !$bundled_prod->has_enough_stock( intval( $bundled_item->get_quantity() ) * intval( $product_quantity ) ) ) {
                        wc_add_notice( __( 'You cannot add this quantity of items, because there are not enough in stock.', 'yith-woocommerce-product-bundles' ), 'error' );

                        return false;
                    }
                }
            }


            return $add_flag;
        }

        /**
         * set bundled product price = 0
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_add_cart_item( $cart_item, $cart_key ) {

            $cart_contents = WC()->cart->cart_contents;

            if ( isset( $cart_item[ 'bundled_by' ] ) ) {
                $bundle_cart_key = $cart_item[ 'bundled_by' ];
                if ( isset( $cart_contents[ $bundle_cart_key ] ) ) {
                    yit_set_prop( $cart_item[ 'data' ], 'price', 0 );
                    yit_set_prop( $cart_item[ 'data' ], 'bundled_item_price_zero', true );
                }
            }

            return $cart_item;
        }

        /**
         * when a bundle product is removed, its bundled items are removed too.
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_cart_item_removed( $cart_item_key, $cart ) {

            if ( !empty( $cart->removed_cart_contents[ $cart_item_key ][ 'bundled_items' ] ) ) {

                $bundled_item_cart_keys = $cart->removed_cart_contents[ $cart_item_key ][ 'bundled_items' ];

                foreach ( $bundled_item_cart_keys as $bundled_item_cart_key ) {

                    if ( !empty( $cart->cart_contents[ $bundled_item_cart_key ] ) ) {

                        $remove                                                = $cart->cart_contents[ $bundled_item_cart_key ];
                        $cart->removed_cart_contents[ $bundled_item_cart_key ] = $remove;

                        unset( $cart->cart_contents[ $bundled_item_cart_key ] );

                        do_action( 'woocommerce_cart_item_removed', $bundled_item_cart_key, $cart );
                    }
                }
            }
        }

        /**
         * when a bundle product is restored, its bundled items are restored too.
         *
         * @access public
         * @since  1.0.19
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         * @param         $cart_item_key
         * @param WC_Cart $cart
         */
        public function woocommerce_cart_item_restored( $cart_item_key, $cart ) {
            if ( !empty( $cart->cart_contents[ $cart_item_key ][ 'bundled_items' ] ) ) {
                $bundled_item_cart_keys = $cart->cart_contents[ $cart_item_key ][ 'bundled_items' ];
                foreach ( $bundled_item_cart_keys as $bundled_item_cart_key ) {
                    $cart->restore_cart_item( $bundled_item_cart_key );
                }
            }
        }

        /**
         * get cart item from session
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_get_cart_item_from_session( $cart_item, $item_session_values, $cart_item_key ) {
            $cart_contents = !empty( WC()->cart ) ? WC()->cart->cart_contents : '';
            if ( isset( $item_session_values[ 'bundled_items' ] ) && !empty( $item_session_values[ 'bundled_items' ] ) )
                $cart_item[ 'bundled_items' ] = $item_session_values[ 'bundled_items' ];

            if ( isset( $item_session_values[ 'cartstamp' ] ) ) {
                $cart_item[ 'cartstamp' ] = $item_session_values[ 'cartstamp' ];
            }

            if ( isset( $item_session_values[ 'bundled_by' ] ) ) {
                $cart_item[ 'bundled_by' ]      = $item_session_values[ 'bundled_by' ];
                $cart_item[ 'bundled_item_id' ] = $item_session_values[ 'bundled_item_id' ];
                $bundle_cart_key                = $cart_item[ 'bundled_by' ];

                if ( isset( $cart_contents[ $bundle_cart_key ] ) ) {
                    yit_set_prop( $cart_item[ 'data' ], 'price', 0 );
                    if ( isset( $cart_item[ 'data' ]->subscription_sign_up_fee ) ) {
                        yit_set_prop( $cart_item[ 'data' ], 'subscription_sign_up_fee', 0 );
                    }
                }
            }

            return $cart_item;
        }


        /* -----------------------------------------
                             ORDER
            ---------------------------------------- */

        /**
         * delete subtotal for bundled items in order
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function woocommerce_order_formatted_line_subtotal( $subtotal, $item, $order ) {
            if ( isset( $item[ 'bundled_by' ] ) )
                return '';

            return $subtotal;
        }

        /**
         * add meta in order
         *
         * @access     public
         * @since      1.0.0
         * @author     Leanza Francesco <leanzafrancesco@gmail.com>
         * @deprecated since 1.2.11
         */
        public function woocommerce_add_order_item_meta( $item_id, $values, $cart_item_key ) {
            //DO NOTHING
        }

        /**
         * add bundle data to order items
         *
         * @param WC_Order_Item_Product $item
         * @param string                $cart_item_key
         * @param array                 $values
         * @param WC_Order              $order
         * @since 1.2.11
         */
        public function woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
            $is_bundle       = isset( $values[ 'cartstamp' ] );
            $is_bundled_item = isset( $values[ 'bundled_by' ] );
            $meta_to_store   = array();

            if ( $is_bundle ) {
                $meta_to_store = array(
                    '_cartstamp' => $values[ 'cartstamp' ],
                );
            } elseif ( $is_bundled_item ) {
                $meta_to_store = array(
                    '_bundled_by' => $values[ 'bundled_by' ],
                );
            }

            if ( $meta_to_store ) {
                foreach ( $meta_to_store as $key => $value ) {
                    $item->add_meta_data( $key, $value );
                }
            }
        }

        public function woocommerce_cart_shipping_packages( $packages ) {

            if ( !empty( $packages ) ) {
                foreach ( $packages as $package_key => $package ) {
                    if ( !empty( $package[ 'contents' ] ) ) {
                        foreach ( $package[ 'contents' ] as $cart_item => $cart_item_data ) {
                            if ( isset( $cart_item_data[ 'bundled_items' ] ) && isset( $cart_item_data[ 'yith_parent' ] ) ) {
                                // SINGULAR SHIPPING
                                $parent_bundle_key = $cart_item_data[ 'yith_parent' ];
                                if ( isset( $package[ 'contents' ][ $parent_bundle_key ] ) ) {
                                    unset( $packages[ $package_key ][ 'contents' ][ $parent_bundle_key ] );
                                }
                            }
                        }
                    }
                }
            }

            return $packages;
        }

        /**
         * set order_needs_processing to false for Bundles
         *
         * @param bool       $needs_processing
         * @param WC_Product $product
         * @param int        $order_id
         * @return bool
         */
        public function woocommerce_order_item_needs_processing( $needs_processing, $product, $order_id ) {
            if ( $product->is_type( 'yith_bundle' ) )
                return false;

            return $needs_processing;
        }

        public function enqueue_scripts() {
            wp_enqueue_style( 'yith_wcpb_bundle_frontend_style', YITH_WCPB_ASSETS_URL . '/css/frontend.css' );
        }

    }
}
/**
 * Unique access to instance of YITH_WCPB_Frontend class
 *
 * @return YITH_WCPB_Frontend|YITH_WCPB_Frontend_Premium
 * @since 1.0.0
 */
function YITH_WCPB_Frontend() {
    return YITH_WCPB_Frontend::get_instance();
}