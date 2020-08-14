<?php
/**
 * Product Bundle Class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Product Bundles
 * @version 1.0.0
 */


if ( !defined( 'YITH_WCPB' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'WC_Product_Yith_Bundle' ) ) {
    /**
     * Product Bundle Object
     *
     * @since  1.0.0
     * @author Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class WC_Product_Yith_Bundle extends WC_Product {

        public  $bundle_data;
        private $bundled_items;

        public $per_items_pricing;
        public $non_bundled_shipping;

        public $price_per_item_tot_max;
        public $price_per_item_tot;

        private $_advanced_options;

        public $out_of_stock_sync;

        /**
         * __construct
         *
         * @access public
         * @param mixed $product
         */
        public function __construct( $product ) {
            if ( !$this instanceof WC_Data )
                $this->product_type = 'yith_bundle';

            parent::__construct( $product );
            $id = $this->get_wpml_parent_id();

            $this->bundle_data = get_post_meta( $id, '_yith_wcpb_bundle_data', true );

            $this->per_items_pricing    = ( get_post_meta( $id, '_yith_wcpb_per_item_pricing', true ) == 'yes' ) ? true : false;
            $this->non_bundled_shipping = ( get_post_meta( $id, '_yith_wcpb_non_bundled_shipping', true ) == 'yes' ) ? true : false;

            $default_advanced_options = array(
                'min' => 0,
                'max' => 0,
            );
            $this->_advanced_options  = get_post_meta( $id, '_yith_wcpb_bundle_advanced_options', true );
            $this->_advanced_options  = wp_parse_args( $this->_advanced_options, $default_advanced_options );

            if ( !empty( $this->bundle_data ) ) {
                $this->load_items();
            }

            if ( $this->per_items_pricing ) {
                $this->price = 0;
            }

            $this->price_per_item_tot_max = $this->get_per_item_price_tot_max();
            $this->price_per_item_tot     = $this->get_per_item_price_tot();

            //$ajax_add_to_cart_enabled = !$this->has_optional() && !$this->has_variables() && !$this->has_quantity_to_choose() && $this->is_purchasable() && $this->is_in_stock() && $this->all_items_in_stock();
            $ajax_add_to_cart_enabled = !$this->has_optional() && !$this->has_variables() && !$this->has_quantity_to_choose();
            if ( $ajax_add_to_cart_enabled ) {
                $this->supports[] = 'ajax_add_to_cart';
            }

            $is_frontend = !is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX );

            $this->out_of_stock_sync = 'yes' === get_option( 'yith-wcpb-bundle-out-of-stock-sync', 'no' ) && $is_frontend;

            if ( $is_frontend && !$this->is_in_stock() ) {
                $this->set_stock_status( 'outofstock' );
            }

        }

        /**
         * set outstock status if items are out of stock and the stock sync is enabled
         *
         * @param string $status
         * @since 1.2.18
         */
        public function set_stock_status( $status = 'instock' ) {
            if ( 'instock' === $status && $this->out_of_stock_sync && !$this->all_items_in_stock() ) {
                $status = 'outofstock';
            }

            parent::set_stock_status( $status );
        }

        public function get_type() {
            return 'yith_bundle';
        }

        public function get_advanced_options( $option = '' ) {
            if ( !$option ) {
                return $this->_advanced_options;
            }

            if ( isset( $this->_advanced_options[ $option ] ) ) {
                return $this->_advanced_options[ $option ];
            }

            return false;
        }

        public function get_wpml_parent_id() {
            global $sitepress;

            $id = $this->get_id();

            if ( isset( $sitepress ) ) {
                $finded = false;

                $details = $sitepress->get_element_language_details( $id );
                if ( $details->trid ) {
                    $current_id  = absint( $details->trid );
                    $bundle_data = get_post_meta( $current_id, '_yith_wcpb_bundle_data', true );
                    if ( !!$bundle_data ) {
                        $id     = $current_id;
                        $finded = true;
                    }
                }

                if ( !$finded ) {
                    $default_language = $sitepress->get_default_language();

                    $source_language = !empty( $details->source_language_code ) ? $details->source_language_code : $default_language;
                    $current_id      = $this->get_id();
                    if ( function_exists( 'icl_object_id' ) ) {
                        $current_id = icl_object_id( $this->get_id(), 'product', true, $source_language );
                    } else if ( function_exists( 'wpml_object_id_filter' ) ) {
                        $current_id = wpml_object_id_filter( $this->get_id(), 'product', true, $source_language );
                    }

                    $bundle_data = get_post_meta( $current_id, '_yith_wcpb_bundle_data', true );
                    if ( !!$bundle_data ) {
                        $id = $current_id;
                    }
                }
            }

            return $id;
        }

        /**
         * Load bundled items
         *
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        private function load_items() {
            $virtual = true;

            foreach ( $this->bundle_data as $b_item_id => $b_item_data ) {
                $product_id = isset( $b_item_data[ 'product_id' ] ) ? $b_item_data[ 'product_id' ] : false;
                if ( !$product_id )
                    continue;

                // load Simple and Variable products only
                $terms        = get_the_terms( $product_id, 'product_type' );
                $product_type = !empty( $terms ) && isset( current( $terms )->name ) ? sanitize_title( current( $terms )->name ) : 'simple';
                if ( !in_array( $product_type, array( 'simple', 'variable' ) ) )
                    continue;

                $b_item = new YITH_WC_Bundled_Item( $this, $b_item_id );
                if ( $b_item->exists() ) {
                    $this->bundled_items[ $b_item_id ] = $b_item;

                    if ( !$b_item->product->is_virtual() ) {
                        $virtual = false;
                    }
                }
            }
            if ( $this->non_bundled_shipping ) {
                $virtual = true;
            }
            yit_set_prop( $this, 'virtual', $virtual );
        }

        /**
         * return bundled items array
         *
         * @return array
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function get_bundled_items() {
            return !empty( $this->bundled_items ) ? $this->bundled_items : array();
        }

        /**
         * Returns whether or not the product is in stock.
         *
         * @return bool
         */
        public function is_in_stock() {
            $status = parent::is_in_stock();

            if ( $status && $this->out_of_stock_sync ) {
                $status = $this->all_items_in_stock();
            }

            return apply_filters( 'yith_wcpb_bundle_product_is_in_stock', $status, $this );
        }

        /**
         * Returns false if the product cannot be bought.
         *
         * @return bool
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function is_purchasable() {
            $purchasable = true;

            // Products must exist of course
            if ( !$this->exists() ) {
                $purchasable = false;
                // Other products types need a price to be set
            } elseif ( !$this->per_items_pricing && $this->get_price() === '' ) {
                $purchasable = false;

                // Check the product is published
            } elseif ( ( $this instanceof WC_Data ? $this->get_status() : $this->post->post_status ) !== 'publish' && !current_user_can( 'edit_post', $this->get_id() ) ) {
                $purchasable = false;

            }

            // Check the bundle items are purchasable
            $bundled_items = $this->get_bundled_items();
            foreach ( $bundled_items as $bundled_item ) {
                if ( $bundled_item->get_product() && !$bundled_item->get_product()->is_type( 'variable' ) && !$bundled_item->get_product()->is_purchasable() ) {
                    $purchasable = false;
                }
            }

            return apply_filters( 'woocommerce_is_purchasable', $purchasable, $this );
        }

        /**
         * Returns true if all items is in stock
         *
         * @return bool
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function all_items_in_stock() {
            $response = true;

            $bundled_items = $this->get_bundled_items();
            foreach ( $bundled_items as $bundled_item ) {
                if ( !$bundled_item->get_product()->is_in_stock() ) {
                    $response = false;
                }
            }

            return $response;
        }

        /**
         * Return true if some item has quantity to choose
         *
         * @return  int
         */
        public function has_quantity_to_choose() {
            $bundled_items = $this->get_bundled_items();
            foreach ( $bundled_items as $bundled_item ) {
                if ( $bundled_item->has_quantity_to_choose() ) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Return true if some item is optional
         *
         * @return  int
         */
        public function has_optional() {
            $bundled_items = $this->get_bundled_items();
            foreach ( $bundled_items as $bundled_item ) {
                if ( $bundled_item->is_optional() ) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Returns true if one item at least is variable product.
         *
         * @return bool
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function has_variables() {
            $bundled_items = $this->get_bundled_items();
            foreach ( $bundled_items as $bundled_item ) {
                if ( $bundled_item->has_variables() ) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Get the add to cart url used in loops.
         *
         * @access public
         * @return string
         */
        public function add_to_cart_url() {
            $url = !$this->has_optional() && !$this->has_variables() && !$this->has_quantity_to_choose() && $this->is_purchasable() && $this->is_in_stock() && $this->all_items_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->get_id() ) ) : get_permalink( $this->get_id() );

            return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
        }

        /**
         * Get the add to cart button text
         *
         * @access public
         * @return string
         */
        public function add_to_cart_text() {
            $text = !$this->has_optional() && !$this->has_variables() && !$this->has_quantity_to_choose() && $this->is_purchasable() && $this->is_in_stock() && $this->all_items_in_stock() ? __( 'Add to cart', 'woocommerce' ) : __( 'Read more', 'woocommerce' );

            return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
        }

        /**
         * get the ID of the parent post
         * added for support to wc 2.7 and wc 2.6
         *
         * @param string $context
         * @return int
         */
        public function get_parent_id( $context = 'view' ) {
            return $this instanceof WC_Data ? parent::get_parent_id( $context ) : $this->get_parent();
        }

        /**
         * Get the title of the post.
         *
         * @access public
         * @return string
         */
        public function get_title() {

            $title = get_the_title( $this->get_id() );

            if ( $this->get_parent_id() > 0 ) {
                $title = get_the_title( $this->get_parent_id() ) . ' &rarr; ' . $title;
            }

            return apply_filters( 'woocommerce_product_title', $title, $this );
        }

        /**
         * Sync grouped products with the children lowest price (so they can be sorted by price accurately).
         *
         * @access public
         * @return void
         */
        public function grouped_product_sync() {
            if ( !$this->get_parent_id() )
                return;

            $children_by_price = get_posts( array(
                                                'post_parent'    => $this->get_parent_id(),
                                                'orderby'        => 'meta_value_num',
                                                'order'          => 'asc',
                                                'meta_key'       => '_price',
                                                'posts_per_page' => 1,
                                                'post_type'      => 'product',
                                                'fields'         => 'ids'
                                            ) );
            if ( $children_by_price ) {
                foreach ( $children_by_price as $child ) {
                    $child_price = get_post_meta( $child, '_price', true );
                    update_post_meta( $this->get_parent_id(), '_price', $child_price );
                }
            }

            delete_transient( 'wc_products_onsale' );

            do_action( 'woocommerce_grouped_product_sync', $this->get_id(), $children_by_price );
        }


        public function get_bundled_item( $item_id ) {
            if ( !empty( $this->bundle_data ) && isset( $this->bundle_data[ $item_id ] ) ) {
                if ( isset( $this->bundled_items[ $item_id ] ) ) {
                    return $this->bundled_items[ $item_id ];
                } else {
                    return new YITH_WC_Bundled_Item( $item_id, $this );
                }
            }

            return false;
        }


        public function get_price( $context = 'view' ) {
            if ( $this->per_items_pricing ) {
                $price = $this instanceof WC_Data ? parent::get_price( 'edit' ) : $this->price;

                return apply_filters( 'woocommerce_yith_bundle_get_price', $price, $this );
            } else {
                return $this instanceof WC_Data ? parent::get_price( $context ) : parent::get_price();
            }
        }


        /**
         * get the MAX price of product bundle
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function get_per_item_price_tot_max( $apply_discount = false, $include_optionals = false, $with_max_quantity = false, $context = 'view' ) {
            if ( 'view' === $context && isset( $this->price_per_item_tot_max ) && !$apply_discount ) {
                return $this->price_per_item_tot_max;
            }
            $args = compact( 'apply_discount', 'include_optionals', 'with_max_quantity' );

            if ( 'view' === $context ) {
                do_action( 'yith_wcpb_before_get_per_item_price_tot_max', $this, $args );
            }

            $price = 0;
            if ( $this->per_items_pricing ) {
                $bundled_items_price = 0;

                $bundled_items = $this->get_bundled_items();

                foreach ( $bundled_items as $bundled_item ) {
                    if ( !$include_optionals && $bundled_item->is_optional() ) {
                        continue;
                    }
                    /**
                     * @var WC_Product $product
                     */
                    $product            = $bundled_item->product;
                    $bundled_item_price = 0;
                    $qty                = max( 0, ( $with_max_quantity ? $bundled_item->max_quantity : $bundled_item->min_quantity ) );
                    if ( !$bundled_item->has_variables() ) {
                        // SIMPLE
                        $bundled_item_price = yith_wcpb_get_price_to_display( $product, $product->get_regular_price() );
                    } else {
                        // VARIABLE
                        if ( $context !== 'view' ) {
                            list ( $min_price, $max_price ) = $bundled_item->get_min_max_prices();
                            $bundled_item_price = yith_wcpb_get_price_to_display( $product, $max_price );
                        } else {
                            $bundled_item_price = yith_wcpb_get_price_to_display( $product, $bundled_item->max_price );
                        }
                    }

                    if ( $apply_discount ) {
                        $discount = $bundled_item_price * (float) $bundled_item->discount / 100;
                        if ( 'view' === $context ) {
                            $discount = apply_filters( 'yith_wcpb_bundled_item_calculated_discount', $discount, $bundled_item->discount, $bundled_item_price, $bundled_item->get_product_id(), array() );
                        }
                        $bundled_item_price -= $discount;
                    }

                    $bundled_item_price  = $bundled_item_price * $qty;
                    $bundled_items_price += $bundled_item_price;
                }
                $price = $bundled_items_price;
            }

            if ( 'view' === $context ) {
                do_action( 'yith_wcpb_after_get_per_item_price_tot_max', $this, $args );
            }

            return 'view' === $context ? apply_filters( 'yith_wcpb_get_per_item_price_tot_max', $price, $this, $args ) : $price;
        }


        /**
         * @param string $deprecated
         * @return mixed|string
         */
        public function get_price_html( $deprecated = '' ) {
            if ( $this->per_items_pricing ) {
                $bundle_price_display = get_option( 'yith-wcpb-pip-bundle-pricing', 'from-min' );
                $min                  = $this->get_per_item_price_tot();
                switch ( $bundle_price_display ) {
                    case 'min-max':
                        $max        = $this->get_per_item_price_tot_max( true, true, true );
                        $price_html = $max > $min ? wc_format_price_range( $min, $max ) : wc_price( $min );
                        break;

                    case 'min':
                        $price_html = wc_price( $min );
                        break;

                    case 'regular-and-discounted':
                        $regular    = $this->get_per_item_price_tot_max( false );
                        $price_html = $regular > $min ? wc_format_sale_price( $regular, $min ) : wc_price( $min );
                        break;

                    case 'from-min':
                    default:
                        $price_html = sprintf( _x( 'From %s', 'From price', 'yith-woocommerce-product-bundles' ), wc_price( $min ) );

                }


            } else {
                $price_html = parent::get_price_html();
            }
            return apply_filters( 'yith_wcpb_woocommerce_get_price_html', $price_html, $this );
        }

        /**
         * get the MIN price of product bundle
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function get_per_item_price_tot( $context = 'view' ) {
            if ( 'view' === $context ) {
                do_action( 'yith_wcpb_before_get_per_item_price_tot', $this );
            }

            $price = 0;
            if ( $this->per_items_pricing ) {
                $bundled_items_price = 0;

                $bundled_items = $this->get_bundled_items();

                foreach ( $bundled_items as $bundled_item ) {
                    if ( $bundled_item->is_optional() ) {
                        continue;
                    }
                    /**
                     * @var WC_Product $product
                     */
                    $product            = $bundled_item->product;
                    $bundled_item_price = 0;
                    $qty                = max( 0, $bundled_item->min_quantity );
                    if ( !$bundled_item->has_variables() ) {
                        // SIMPLE
                        $bundled_item_price = yith_wcpb_get_price_to_display( $product, $product->get_regular_price() );
                    } else {
                        if ( $context !== 'view' ) {
                            list ( $min_price, $max_price ) = $bundled_item->get_min_max_prices();
                            $bundled_item_price = yith_wcpb_get_price_to_display( $product, $min_price );
                        } else {
                            $bundled_item_price = yith_wcpb_get_price_to_display( $product, $bundled_item->min_price );
                        }
                    }

                    $discount            = $bundled_item_price * (float) $bundled_item->discount / 100;
                    $discount            = 'view' === $context ? apply_filters( 'yith_wcpb_bundled_item_calculated_discount', $discount, $bundled_item->discount, $bundled_item_price, $bundled_item->get_product_id(), array() ) : $discount;
                    $bundled_item_price  -= $discount;
                    $bundled_item_price  = $bundled_item_price * $qty;
                    $bundled_items_price += $bundled_item_price;
                }
                $price = $bundled_items_price;
            }

            if ( 'view' === $context ) {
                do_action( 'yith_wcpb_after_get_per_item_price_tot', $this );
            }

            return 'view' === $context ? apply_filters( 'yith_wcpb_get_per_item_price_tot', $price, $this ) : $price;
        }

        public function get_price_html_from_to( $from, $to ) {
            if ( !$this->per_items_pricing || ( !$this->has_variables() && !$this->has_optional() ) ) {
                if ( $this instanceof WC_Data ) {
                    return apply_filters( 'woocommerce_get_price_html_from_to', wc_format_price_range( $from, $to ), $from, $to, $this );
                } else {
                    return parent::get_price_html_from_to( $from, $to );
                }
            } else {
                return wc_price( $to ) . '-' . wc_price( $from );
            }
        }


        public function get_per_item_price_tot_with_params( $array_quantity, $array_opt, $array_var, $html = true, $context = 'view' ) {
            if ( !is_array( $array_quantity ) ) {
                return $this->price_per_item_tot;
            }

            if ( 'view' === $context )
                do_action( 'yith_wcpb_before_get_per_item_price_tot_with_params', $array_quantity, $array_opt, $array_var, $html );

            $price = yit_get_prop( $this, 'price', true, 'edit' );

            if ( $this->per_items_pricing ) {
                $bundled_items_price = 0;
                $bundled_items       = $this->get_bundled_items();
                $loop                = 0;

                foreach ( $bundled_items as $bundled_item ) {
                    if ( $bundled_item->is_optional() && ( ( isset( $array_opt[ $loop ] ) && $array_opt[ $loop ] == '0' ) || !isset( $array_opt[ $loop ] ) ) ) {
                        $loop++;
                        continue;
                    }

                    /**
                     * @var WC_Product $product
                     */
                    $product = $bundled_item->product;
                    if ( $product->is_type( 'variable' ) ) {
                        if ( isset( $array_var[ $loop ] ) && $variation = wc_get_product( $array_var[ $loop ] ) ) {
                            $product       = $variation;
                            $regular_price = yith_wcpb_get_price_to_display( $product, $product->get_regular_price() );
                        } else {

                            if ( $context !== 'view' ) {
                                list ( $min_price, $max_price ) = $bundled_item->get_min_max_prices();
                                $regular_price = yith_wcpb_get_price_to_display( $product, $min_price );
                            } else {
                                $regular_price = yith_wcpb_get_price_to_display( $product, $bundled_item->min_price );
                            }
                        }
                    } else {
                        $regular_price = yith_wcpb_get_price_to_display( $product, $product->get_regular_price() );
                    }

                    $qty = !empty( $array_quantity[ $loop ] ) ? $array_quantity[ $loop ] : max( 0, $bundled_item->min_quantity );

                    $bundled_item_price = $regular_price;

                    $discount           = $bundled_item_price * $bundled_item->discount / 100;
                    $discount           = apply_filters( 'yith_wcpb_bundled_item_calculated_discount', $discount, $bundled_item->discount, $bundled_item_price, $product->get_id(), array() );
                    $bundled_item_price -= $discount;
                    $bundled_item_price *= $qty;

                    $bundled_items_price += $bundled_item_price;
                    $loop++;
                }

                $price = $bundled_items_price;
            }

            if ( 'view' === $context )
                do_action( 'yith_wcpb_after_get_per_item_price_tot_with_params', $array_quantity, $array_opt, $array_var, $html, $price );

            return $html ? wc_price( $price ) : $price;
        }


        /**
         * Gets product variation data which is passed to JS.
         *
         * @return array variation data array
         */
        public function get_available_bundle_variations() {
            if ( !$this->get_bundled_items() ) {
                return array();
            }

            $bundle_variations = array();
            $price_zero        = !$this->per_items_pricing;
            foreach ( $this->get_bundled_items() as $bundled_item )
                $bundle_variations[ $bundled_item->item_id ] = $bundled_item->get_product_variations( $price_zero );

            return $bundle_variations;
        }

        /**
         * Gets the attributes of all variable bundled items.
         *
         * @return array attributes array
         */
        public function get_bundle_variation_attributes() {

            if ( !$this->get_bundled_items() ) {
                return array();
            }

            $bundle_attributes = array();

            foreach ( $this->get_bundled_items() as $bundled_item ) {
                $bundle_attributes[ $bundled_item->item_id ] = $bundled_item->get_product_variation_attributes();
            }

            return $bundle_attributes;
        }

        public function get_selected_bundle_variation_attributes() {

            if ( !$this->get_bundled_items() ) {
                return array();
            }

            $seleted_bundle_attributes = array();

            foreach ( $this->get_bundled_items() as $bundled_item ) {
                $seleted_bundle_attributes[ $bundled_item->item_id ] = $bundled_item->get_selected_product_variation_attributes();
            }

            return $seleted_bundle_attributes;
        }


    }
}