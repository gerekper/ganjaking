<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * YITH WooCommerce Request a Quote Compatibility Class
 *
 * @class   YITH_WCPB_Request_A_Quote_Compatibility
 * @package Yithemes
 * @since   1.0.28
 * @author  Yithemes
 */
class YITH_WCPB_Request_A_Quote_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCPB_Request_A_Quote_Compatibility
     */
    protected static $instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCPB_Request_A_Quote_Compatibility
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Constructor
     *
     * @access public
     */
    private function __construct() {

	    add_filter( 'yith_wcpb_woocommerce_get_price_html', array( $this, 'show_product_price' ), 10 );
	    add_filter( 'yith_wcpb_show_bundled_items_prices', array( $this, 'check_show_bundled_items_prices' ), 10, 3 );
	    add_filter( 'yith_wcpb_ajax_update_price_enabled', array( $this, 'check_ajax_update_price_enabled' ), 10, 2 );
        // Request a quote Table

        add_filter( 'ywraq_add_item', array( $this, 'add_bundle_to_quote' ), 11, 2 );
        add_action( 'ywraq_after_request_quote_view_item', array( $this, 'add_bundle_in_raq_table' ), 10, 2 );
        add_action( 'ywraq_after_request_quote_view_item_on_email', array( $this, 'add_bundle_in_raq_table' ), 10, 2 );

        add_action( 'ywraq_quote_adjust_price', array( $this, 'adjust_bundle_price' ), 10, 2 );

        add_action( 'ywraq_from_cart_to_order_item', array( $this, 'add_order_item_meta' ), 10, 4 );

        add_filter( 'ywraq_order_cart_item_data', array( $this, 'order_cart_item_data' ), 10, 3 );

        add_filter( 'ywraq_add_to_cart_validation', array( $this, 'remove_bundled_items' ), 10, 6 );

        add_filter( 'yith_wcpb_woocommerce_get_cart_item_from_session', array( $this, 'bundled_by_from_session' ), 10, 1 );
        add_filter( 'yith_wcpb_woocommerce_cart_item_price', array( $this, 'set_bundle_price' ), 10, 4 );
        add_filter( 'yith_wcpb_woocommerce_check_cart_items_for_bundle', array( $this, 'not_check_cart_items_for_raq_bundle' ), 10, 2 );

        add_filter( 'ywraq_formatted_line_total', array( $this, 'order_bundled_item_subtotal' ), 99, 3 );


        add_filter( 'yith_ywraq_item_class', array( $this, 'add_bundle_parent_class' ), 10, 3 );
        add_filter( 'yith_ywraq_item_attributes', array( $this, 'add_bundle_attributes' ), 10, 3 );


        //add_filter( 'yith_ywraq_product_price', array( $this, 'bundle_price_in_raq_table_total' ), 10, 3 );
        //add_filter( 'yith_ywraq_product_price_html', array( $this, 'bundle_price_in_raq_table' ), 10, 3 );
    }

	/**
	 * Check for which users will not see the price
	 *
	 * @since   1.0.0
	 * @author  Emanuela Castorina
	 *
	 * @param      $price
	 * @param bool $product_id
	 *
	 * @return string
	 */
	public function show_product_price( $price ) {

		$hide_price = get_option( 'ywraq_hide_price' ) == 'yes';

		return ( $hide_price ) ? '' : $price;

	}

	/**
	 * @param bool                   $value
	 * @param YITH_WC_Bundled_Item   $bundled_item
	 * @param WC_Product_Yith_Bundle $product
	 *
	 * @since   1.1.4
	 * @return bool
	 */
	public function check_show_bundled_items_prices( $value, $bundled_item, $product ) {

		return get_option( 'ywraq_hide_price' ) != 'yes';
	}

	/**
	 * @param bool                   $value
	 * @param WC_Product_Yith_Bundle $product
	 *
	 * @since   1.1.4
	 * @return bool
	 */
	public function check_ajax_update_price_enabled( $value, $product ) {

		return get_option( 'ywraq_hide_price' ) != 'yes';
	}


    /* --------------------------- Request a Quote TABLE ------------------------------ */

    public function adjust_bundle_price( $raq, $product ) {

        if ( $product->is_type( 'yith_bundle' ) ) {
            /** @var WC_Product_Yith_Bundle $product */
            if ( $product->per_items_pricing ) {
                $raq_quantity = isset( $raq[ 'quantity' ] ) ? $raq[ 'quantity' ] : 1;
                $price        = $this->get_bundle_price_from_raq( $product, $raq );
                $product->set_price( $price / $raq_quantity );
            }
        }
    }

    /**
     * @param array $raq
     * @param array $product_raq
     * @return mixed
     */
    public function add_bundle_to_quote( $raq, $product_raq ) {
        $product_id = isset( $raq[ 'product_id' ] ) ? $raq[ 'product_id' ] : false;
        if ( $product_id ) {
            $product = wc_get_product( $product_id );
            if ( $product && $product->is_type( 'yith_bundle' ) ) {
                $bundle_info = array();
                foreach ( $product_raq as $key => $value ) {
                    if ( strpos( $key, 'yith_bundle_' ) === 0 ) {
                        $bundle_info[ $key ] = $value;
                    }
                }

                //$raq                  = $product_raq;
                $raq[ 'yith-bundle-add-to-cart-params' ] = $bundle_info;
            }
        }

        return $raq;
    }

    /**
     * @param array  $raq_content
     * @param string $key
     */
    public function add_bundle_in_raq_table( $raq_content, $key ) {
        $raq_info   = $raq_content[ $key ];
        $product_id = $raq_info[ 'product_id' ];
        if ( $product_id ) {
            $product = wc_get_product( $product_id );
            if ( $product && $product->is_type( 'yith_bundle' ) ) {
                /**
                 * @var WC_Product_Yith_Bundle $product
                 */
                $bundled_items = $product->get_bundled_items();

                $raq_quantity = isset( $raq_info[ 'quantity' ] ) ? $raq_info[ 'quantity' ] : 1;
                $bundle_info  = isset( $raq_info[ 'yith-bundle-add-to-cart-params' ] ) ? $raq_info[ 'yith-bundle-add-to-cart-params' ] : array();
                if ( $bundled_items ) {
                    foreach ( $bundled_items as $item ) {
                        /**
                         * @var YITH_WC_Bundled_Item $item
                         */
                        $id = $item->item_id;

                        if ( $item->is_hidden() )
                            continue;

                        if ( $item->is_optional() && !isset( $bundle_info[ 'yith_bundle_optional_' . $id ] ) )
                            continue;

                        $quantity = isset( $bundle_info[ 'yith_bundle_quantity_' . $id ] ) ? $bundle_info[ 'yith_bundle_quantity_' . $id ] : 0;

                        if ( !$quantity )
                            continue;

                        $variation_id = '';
                        $variations   = array();
                        $prod         = $item->product;

                        if ( isset( $bundle_info[ 'yith_bundle_variation_id_' . $id ] ) ) {
                            $bundle_variations = $product->get_bundle_variation_attributes();
                            $variations        = isset( $bundle_variations[ $id ] ) ? $bundle_variations[ $id ] : array();
                            $product_id        = $bundle_info[ 'yith_bundle_variation_id_' . $id ];
                            $variation_id      = $product_id;

                            $prod = wc_get_product( $product_id );
                        }

                        if ( !$prod )
                            continue;

                        $quantity = $quantity * $raq_quantity;

                        $link  = $prod->is_visible() ? get_permalink( $prod->get_id() ) : '#';
                        $title = esc_attr( $prod->get_name() );
                        $image = !$item->hide_thumbnail ? $this->get_image( $prod ) : '';

                        $is_raq_email = current_action() === 'ywraq_after_request_quote_view_item_on_email';

                        $bundled_item_data = compact( 'id', 'prod', 'link', 'title', 'image', 'quantity', 'variation_id', 'variations', 'raq_info', 'raq_content', 'key' );

                        $template = !$is_raq_email ? 'compatibility/request-a-quote/raq-table-row.php' : 'compatibility/request-a-quote/raq-table-row-email.php';
                        wc_get_template( $template, $bundled_item_data, '', YITH_WCPB_TEMPLATE_PATH . '/premium/' );
                    }
                }
            }
        }
    }

    /**
     * @param $product
     * @return string
     */
    public function get_image( $product ) {
        $dimensions = wc_get_image_size( 'shop_thumbnail' );
        $height     = esc_attr( $dimensions[ 'height' ] );
        $width      = esc_attr( $dimensions[ 'width' ] );
        $src        = ( $product->get_image_id() ) ? current( wp_get_attachment_image_src( $product->get_image_id(), 'shop_thumbnail' ) ) : wc_placeholder_img_src();

        $link = '<a href="' . $product->get_permalink() . '"><img src="' . $src . '" height="' . $height . '" width="' . $width . '" /></a>';

        return $link;
    }

    /**
     * @param string $attributes
     * @param array  $raq_content
     * @param string $key
     * @return string
     */
    public function add_bundle_attributes( $attributes, $raq_content, $key ) {
        $raq_info   = $raq_content[ $key ];
        $product_id = $raq_info[ 'product_id' ];
        if ( $product_id ) {
            $product = wc_get_product( $product_id );
            if ( $product && $product->is_type( 'yith_bundle' ) ) {
                $attributes .= ' data-bundle-key="' . $key . '""';
            }
        }

        return $attributes;
    }

    /**
     * @param string $class
     * @param array  $raq_content
     * @param string $key
     * @return string
     */
    public function add_bundle_parent_class( $class, $raq_content, $key ) {
        $raq_info   = $raq_content[ $key ];
        $product_id = $raq_info[ 'product_id' ];
        if ( $product_id ) {
            $product = wc_get_product( $product_id );
            if ( $product && $product->is_type( 'yith_bundle' ) ) {
                $class .= ' bundle-parent';
            }
        }

        return $class;
    }

    /**
     * @param string     $price
     * @param WC_Product $product
     * @param array      $raq
     * @return string
     */
    public function bundle_price_in_raq_table_total( $price, $product, $raq ) {
        if ( $product->is_type( 'yith_bundle' ) ) {
            /**
             * @var WC_Product_Yith_Bundle $product
             */
            if ( $product->per_items_pricing ) {
                $price = $this->get_bundle_price_from_raq( $product, $raq );
            }
        }

        return $price;
    }

    public function bundle_price_in_raq_table( $price, $product, $raq ) {
        if ( $product->is_type( 'yith_bundle' ) ) {
            /**
             * @var WC_Product_Yith_Bundle $product
             */
            if ( $product->per_items_pricing ) {
                $price = $this->get_bundle_price_from_raq( $product, $raq );
                $price = wc_price( $price );
            }
        }

        return $price;
    }

    /**
     * @param WC_Product_Yith_Bundle $product
     * @param array                  $raq
     * @return mixed|string|void
     */
    public function get_bundle_price_from_raq( $product, $raq ) {
        $raq_quantity = isset( $raq[ 'quantity' ] ) ? $raq[ 'quantity' ] : 1;

        if ( $product->is_type( 'yith_bundle' ) ) {
            if ( $product->per_items_pricing ) {
                $bundled_items = $product->get_bundled_items();

                $array_quantity = array();
                $array_opt      = array();
                $array_var      = array();
                if ( $bundled_items ) {
                    $loop        = 0;
                    $bundle_info = isset( $raq[ 'yith-bundle-add-to-cart-params' ] ) ? $raq[ 'yith-bundle-add-to-cart-params' ] : array();
                    foreach ( $bundled_items as $item ) {
                        /**
                         * @var YITH_WC_Bundled_Item $item
                         */
                        $id = $item->item_id;

                        if ( $item->is_optional() && isset( $bundle_info[ 'yith_bundle_optional_' . $id ] ) ) {
                            $array_opt[ $loop ] = 1;
                        }

                        if ( isset( $bundle_info[ 'yith_bundle_quantity_' . $id ] ) ) {
                            $array_quantity[ $loop ] = $bundle_info[ 'yith_bundle_quantity_' . $id ];
                        } else {
                            $array_quantity[ $loop ] = 0;
                        }

                        if ( isset( $bundle_info[ 'yith_bundle_variation_id_' . $id ] ) ) {
                            $array_var[ $loop ] = $bundle_info[ 'yith_bundle_variation_id_' . $id ];
                        } else {
                            $array_var[ $loop ] = '';
                        }
                        $loop++;
                    }

                    //v( compact( 'array_quantity', 'array_opt', 'array_var' ) );

                    $price = $product->get_per_item_price_tot_with_params( $array_quantity, $array_opt, $array_var, false );
                    //v( compact( 'price', 'raq_quantity') );

                    $price = $price * $raq_quantity;

                    return $price;
                }
            }
        }

        return $product->get_price() * $raq_quantity;
    }

    /**
     * @param string   $line_total
     * @param array    $item
     * @param WC_Order $order
     * @return string
     */
    public function order_bundled_item_subtotal( $line_total, $item, $order ) {
        if ( isset( $item[ 'bundled_by' ] ) ) {
            return '';
        }

        return $line_total;
    }

    /**
     * @param array $cart_item
     * @return array
     */
    public function bundled_by_from_session( $cart_item ) {
        $cart_contents = !empty( WC()->cart ) ? WC()->cart->cart_contents : '';
        if ( isset( $cart_item[ 'bundled_by' ] ) ) {
            $bundle_cart_key = $cart_item[ 'bundled_by' ];

            if ( isset( $cart_contents[ $bundle_cart_key ] ) ) {
                $parent = $cart_contents[ $bundle_cart_key ][ 'data' ];
                if ( $parent->per_items_pricing == false || isset( $cart_contents[ $bundle_cart_key ][ 'ywraq_price' ] ) ) {
                    $price = isset( $cart_item[ 'ywraq_price' ] ) ? $cart_item[ 'ywraq_price' ] : 0;
                    $cart_item[ 'data' ]->set_price( $price );
                }
            }
        }
        return $cart_item;
    }

    public function set_bundle_price( $price, $bundled_items_price, $cart_item, $cart_item_key ) {
        if ( isset( $cart_item[ 'bundled_items' ] ) && isset( $cart_item[ 'ywraq_price' ] ) ) {
            return wc_price( $cart_item[ 'ywraq_price' ] );
        }

        return $price;
    }

    public function not_check_cart_items_for_raq_bundle( $check, $item ) {

        return $check && !isset( $item[ 'ywraq_price' ] );
    }

    /**
     * @param string $subtotal
     * @param array  $cart_item
     * @param string $cart_item_key
     * @return string
     */
    public function bundles_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
        if ( isset( $cart_item[ 'bundled_items' ] ) && isset( $cart_item[ 'ywraq_price' ] ) ) {
            return wc_price( $cart_item[ 'ywraq_price' ] );
        }

        return $subtotal;
    }

    /**
     * @param bool  $valid
     * @param int   $product_id
     * @param int   $quantity
     * @param int   $variation_id
     * @param array $variations
     * @param array $cart_item_data
     * @return bool
     */
    public function remove_bundled_items( $valid, $product_id, $quantity, $variation_id, $variations, $cart_item_data ) {
        if ( isset( $cart_item_data[ 'bundled_by' ] ) ) {
            return false;
        }

        return $valid;
    }

    /**
     * @param array    $cart_item_data
     * @param array    $item
     * @param WC_Order $order
     * @return mixed
     */
    public function order_cart_item_data( $cart_item_data, $item, $order ) {
        $to_copy = array(
            'yith-bundle-add-to-cart-params',
            'cartstamp',
            'bundled_by',
            'yith_parent',
            'bundled_items',
            'per_items_pricing',
            'non_bundled_shipping',
            'yith_bundle_cart_key',
        );

        foreach ( $to_copy as $c ) {
            if ( isset( $item[ $c ] ) ) {
                $cart_item_data[ $c ] = maybe_unserialize( $item[ $c ] );
            }
        }

        $cart_item_data[ 'yith_bundle_from_raq' ] = true;

        return $cart_item_data;
    }

    /**
     * @param array    $values
     * @param string   $cart_item_key
     * @param int      $item_id
     * @param WC_Order $order
     * @throws Exception
     */
    public function add_order_item_meta( $values, $cart_item_key, $item_id, $order ) {
        $item = $order->get_item( $item_id );
        if ( $item instanceof WC_Order_Item_Product ) {
            YITH_WCPB_Frontend()->woocommerce_checkout_create_order_line_item( $item, $cart_item_key, $values, $order );
            $item->save();
        }

        if ( isset( $values[ 'yith-bundle-add-to-cart-params' ] ) ) {
            wc_add_order_item_meta( $item_id, 'yith-bundle-add-to-cart-params', $values[ 'yith-bundle-add-to-cart-params' ] );
        }
    }


}

/**
 * Unique access to instance of YITH_WCPB_Request_A_Quote_Compatibility class
 *
 * @return YITH_WCPB_Request_A_Quote_Compatibility
 */
function YITH_WCPB_Request_A_Quote_Compatibility() {
    return YITH_WCPB_Request_A_Quote_Compatibility::get_instance();
}