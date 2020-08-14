<?php
/**
 * Admin class
 *
 * @author  Yithemes
 * @package YITH WooCommerce Bulk Edit Products
 * @version 1.0.0
 */

if ( !defined( 'YITH_WCBEP' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCBEP_Admin_Premium' ) ) {
    /**
     * Admin class.
     * The class manage all the admin behaviors.
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCBEP_Admin_Premium extends YITH_WCBEP_Admin {

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        protected function __construct() {
            parent::__construct();

            YITH_WCBEP_Custom_Fields_Manager();
            YITH_WCBEP_Custom_Taxonomies_Manager();

            add_action( 'wp_ajax_yith_wcbep_save_default_hidden_cols', array( $this, 'save_default_hidden_cols' ) );
            add_action( 'wp_ajax_yith_wcbep_save_enabled_columns', array( $this, 'save_enabled_columns' ) );
            add_action( 'wp_ajax_yith_wcbep_get_image_gallery_uploader', array( $this, 'get_image_gallery_uploader' ) );
            add_action( 'wp_ajax_yith_wcbep_bulk_delete_products', array( $this, 'delete_products' ) );

            add_filter( 'yith_wcbep_settings_admin_tabs', array( $this, 'add_premium_settings_tabs' ) );

            add_action( 'yith_wcbep_enabled_columns_tab', array( $this, 'render_enabled_columns_tab' ) );

            // register plugin to licence/update system
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            add_filter( 'woocommerce_product_export_product_query_args', array( $this, 'filter_product_ids_to_export' ) );
        }

        public function filter_product_ids_to_export( $args ) {
            $form = array();
            if ( !empty( $_REQUEST[ 'form' ] ) ) {
                parse_str( $_REQUEST[ 'form' ], $form );
            }
            if ( !empty( $form[ 'yith-wcbep-selected-products' ] ) ) {
                $ids               = json_decode( $form[ 'yith-wcbep-selected-products' ] );
                $args[ 'include' ] = $ids;
            }
            return $args;
        }

        public function add_premium_settings_tabs( $tabs ) {
            $tabs[ 'enabled-columns' ] = __( 'Enabled Columns', 'yith-woocommerce-bulk-product-editing' );
            $tabs[ 'settings' ]        = __( 'Settings', 'yith-woocommerce-bulk-product-editing' );

            return $tabs;
        }

        public function render_enabled_columns_tab() {
            wc_get_template( 'enabled-columns-tab.php', array(), '', YITH_WCBEP_TEMPLATE_PATH . '/premium/panel/' );
        }


        /**
         * Delete products [AJAX]
         *
         * @access public
         * @since  1.0.0
         */
        public function delete_products() {
            if ( isset( $_POST[ 'products_to_delete' ] ) && is_array( $_POST[ 'products_to_delete' ] ) ) {
                $products_to_delete = $_POST[ 'products_to_delete' ];
                $counter            = 0;
                foreach ( $products_to_delete as $del_id ) {
                    wp_delete_post( absint( $del_id ), true );
                    $counter++;
                }

                if ( $counter > 0 )
                    echo sprintf( _n( '%s product deleted', '%s products deleted', $counter, 'yith-woocommerce-bulk-product-editing' ), $counter );
            }
            die();
        }

        /**
         * Save default hidden cols in table
         *
         * @access public
         * @since  1.0.0
         */
        public function save_default_hidden_cols() {
            if ( current_user_can( 'manage_options' ) || 'yes' === get_option( 'yith-wcbep-hidden-columns-per-user', 'no' ) ) {
                $hidden_columns = isset( $_POST[ 'hidden_cols' ] ) ? $_POST[ 'hidden_cols' ] : array();
                yith_wcbep_set_hidden_columns( $hidden_columns );
            }
            die();
        }

        /**
         * Save enabled columns in table
         *
         * @access public
         * @since  1.1.8
         */
        public function save_enabled_columns() {
            $enabled_columns = isset( $_POST[ 'enabled_columns' ] ) ? $_POST[ 'enabled_columns' ] : array();
            update_option( 'yith_wcbep_enabled_columns', $enabled_columns );

            die();
        }

        public function get_image_gallery_uploader() {
            if ( isset( $_POST[ 'post_id' ] ) ) {
                $post = get_post( $_POST[ 'post_id' ] );
                WC_Meta_Box_Product_Images::output( $post );
            }
            die();
        }

        /**
         * Get table [AJAX]
         *
         * @access public
         * @since  1.0.0
         */
        public function ajax_fetch_table_callback() {
            // Disable display_errors during this ajax requests to prevent malformed JSON
            $current_error_reporting = error_reporting();
            error_reporting( 0 );

            $table = new YITH_WCBEP_List_Table_Premium();
            $table->ajax_response();

            // Enable display_errors
            error_reporting( $current_error_reporting );
        }

        /**
         * Get main-tab template
         *
         * @access public
         * @since  1.0.0
         */
        public function main_tab() {
            $args         = array();
            $premium_path = YITH_WCBEP_TEMPLATE_PATH . '/premium/';

            wc_get_template( 'main-tab-custom-input.php', $args, '', $premium_path );
            wc_get_template( 'main-tab-filters-and-table.php', $args, '', $premium_path );
            wc_get_template( 'main-tab-bulk-editor.php', $args, '', $premium_path );
            wc_get_template( 'main-tab-columns-settings.php', $args, '', $premium_path );
        }

        /**
         * Get table [AJAX]
         *
         * @access public
         * @since  1.0.0
         */
        public function get_table_ajax() {
            $table = new YITH_WCBEP_List_Table_Premium();
            $table->prepare_items();
            $table->display();
            die();
        }

        public function ajax_edit_product() {

        }

        /**
         * Bulk Edit Products [AJAX]
         *
         * @access public
         * @since  1.0.0
         */
        public function bulk_edit_products() {
            global $pagenow;
            if ( isset( $_POST[ 'keys' ] ) && isset( $_POST[ 'row' ] ) && isset( $_POST[ 'edited' ] ) ) {
                $keys    = $_POST[ 'keys' ];
                $row     = $_POST[ 'row' ];
                $edited  = $_POST[ 'edited' ];
                $to_edit = array();

                foreach ( $edited as $column_id => $value ) {
                    if ( $value == 0 && $column_id != 2 ) {
                        $row[ $column_id ] = null;
                    } else {
                        if ( isset( $keys[ $column_id ] ) ) {
                            $to_edit[ $keys[ $column_id ] ] = $value;
                        }
                    }
                }

                do_action( 'yith_wcbep_before_bulk_edit_product', compact( 'keys', 'row', 'edited', 'to_edit' ) );

                $id_index         = array_search( 'ID', $keys );
                $reg_price_index  = array_search( 'regular_price', $keys );
                $sale_price_index = array_search( 'sale_price', $keys );

                $title_index         = array_search( 'title', $keys );
                $slug_index          = array_search( 'slug', $keys );
                $sku_index           = array_search( 'sku', $keys );
                $image_index         = array_search( 'image', $keys );
                $image_gallery_index = array_search( 'image_gallery', $keys );
                $description_index   = array_search( 'description', $keys );
                $shortdesc_index     = array_search( 'shortdesc', $keys );
                $categories_index    = array_search( 'categories', $keys );
                $tags_index          = array_search( 'tags', $keys );

                $weight_index         = array_search( 'weight', $keys );
                $height_index         = array_search( 'height', $keys );
                $width_index          = array_search( 'width', $keys );
                $length_index         = array_search( 'length', $keys );
                $stock_quantity_index = array_search( 'stock_quantity', $keys );

                $purchase_note_index   = array_search( 'purchase_note', $keys );
                $download_limit_index  = array_search( 'download_limit', $keys );
                $download_expiry_index = array_search( 'download_expiry', $keys );
                $menu_order_index      = array_search( 'menu_order', $keys );

                $stock_status_index      = array_search( 'stock_status', $keys );
                $manage_stock_index      = array_search( 'manage_stock', $keys );
                $sold_individually_index = array_search( 'sold_individually', $keys );
                $featured_index          = array_search( 'featured', $keys );
                $virtual_index           = array_search( 'virtual', $keys );
                $downloadable_index      = array_search( 'downloadable', $keys );
                $enable_reviews_index    = array_search( 'enable_reviews', $keys );

                $tax_status_index     = array_search( 'tax_status', $keys );
                $tax_class_index      = array_search( 'tax_class', $keys );
                $backorders_index     = array_search( 'allow_backorders', $keys );
                $shipping_class_index = array_search( 'shipping_class', $keys );
                $status_index         = array_search( 'status', $keys );
                $visibility_index     = array_search( 'visibility', $keys );

                $download_type_index = array_search( 'download_type', $keys );
                $prod_type_index     = array_search( 'prod_type', $keys );

                $date_index            = array_search( 'date', $keys );
                $sale_price_from_index = array_search( 'sale_price_from', $keys );
                $sale_price_to_index   = array_search( 'sale_price_to', $keys );

                $button_text_index = array_search( 'button_text', $keys );
                $product_url_index = array_search( 'product_url', $keys );

                $upsells_index    = array_search( 'up_sells', $keys );
                $crosssells_index = array_search( 'cross_sells', $keys );

                $downloadable_files_index = array_search( 'downloadable_files', $keys );

                // ATTRIBUTES
                $attributes_indexes   = array();
                $attribute_taxonomies = wc_get_attribute_taxonomies();
                if ( $attribute_taxonomies ) {
                    foreach ( $attribute_taxonomies as $tax ) {
                        $attribute_taxonomy_name                        = wc_attribute_taxonomy_name( $tax->attribute_name );
                        $attributes_indexes[ $attribute_taxonomy_name ] = array_search( 'attr_' . $attribute_taxonomy_name, $keys );
                    }
                }

                $counter     = 0;
                $counter_new = 0;

                $id            = $row[ $id_index ];
                $reg_price     = $row[ $reg_price_index ];
                $sale_price    = $row[ $sale_price_index ];
                $title         = $row[ $title_index ];
                $slug          = $row[ $slug_index ];
                $sku           = $row[ $sku_index ];
                $image         = $row[ $image_index ];
                $image_gallery = $row[ $image_gallery_index ];
                $description   = $row[ $description_index ];
                $shortdesc     = $row[ $shortdesc_index ];
                $categories    = $row[ $categories_index ];
                $tags          = $row[ $tags_index ];

                $weight         = $row[ $weight_index ];
                $height         = $row[ $height_index ];
                $width          = $row[ $width_index ];
                $length         = $row[ $length_index ];
                $stock_quantity = $row[ $stock_quantity_index ];

                $purchase_note   = $row[ $purchase_note_index ];
                $download_limit  = $row[ $download_limit_index ];
                $download_expiry = $row[ $download_expiry_index ];
                $menu_order      = $row[ $menu_order_index ];

                $stock_status = $row[ $stock_status_index ];

                $manage_stock = null;
                if ( $row[ $manage_stock_index ] != null ) {
                    $manage_stock = ( $row[ $manage_stock_index ] == '1' ) ? 'yes' : 'no';
                }

                $sold_individually = null;
                if ( $row[ $sold_individually_index ] != null ) {
                    $sold_individually = ( $row[ $sold_individually_index ] == '1' ) ? 'yes' : 'no';
                }
                $featured = null;
                if ( $row[ $featured_index ] != null ) {
                    $featured = ( $row[ $featured_index ] == '1' ) ? 'yes' : 'no';
                }
                $virtual = null;
                if ( $row[ $virtual_index ] != null ) {
                    $virtual = ( $row[ $virtual_index ] == '1' ) ? 'yes' : 'no';
                }
                $downloadable = null;
                if ( $row[ $downloadable_index ] != null ) {
                    $downloadable = ( $row[ $downloadable_index ] == '1' ) ? 'yes' : 'no';
                }
                $enable_reviews = null;
                if ( $row[ $enable_reviews_index ] != null ) {
                    $enable_reviews = ( $row[ $enable_reviews_index ] == '1' ) ? 'open' : 'closed';
                }

                $tax_status     = $row[ $tax_status_index ];
                $tax_class      = $row[ $tax_class_index ];
                $backorders     = $row[ $backorders_index ];
                $shipping_class = $row[ $shipping_class_index ];
                $status         = $row[ $status_index ];
                $visibility     = $row[ $visibility_index ];

                $download_type = $row[ $download_type_index ];
                $prod_type     = $row[ $prod_type_index ];

                $date            = $row[ $date_index ];
                $sale_price_from = $row[ $sale_price_from_index ];
                $sale_price_to   = $row[ $sale_price_to_index ];

                $button_text = $row[ $button_text_index ];
                $product_url = $row[ $product_url_index ];

                $upsells = null;
                if ( $row[ $upsells_index ] !== null ) {
                    $upsells = isset( $row[ $upsells_index ] ) ? array_filter( array_map( 'intval', explode( ',', $row[ $upsells_index ] ) ) ) : array();
                }
                $crosssells = null;
                if ( $row[ $crosssells_index ] !== null ) {
                    $crosssells = isset( $row[ $crosssells_index ] ) ? array_filter( array_map( 'intval', explode( ',', $row[ $crosssells_index ] ) ) ) : array();
                }

                $downloadable_files = $row[ $downloadable_files_index ];

                $attributes_array = array();
                foreach ( $attributes_indexes as $key => $value ) {
                    //$attributes_array[$key] = json_decode( $row[ $value ] );
                    $attributes_array[ $key ] = $row[ $value ];
                };

                $product        = null;
                $is_new_product = false;
                if ( $id === 'NEW' && $title ) {
                    $counter_new++;
                    $counter--;
                    $new_post = array(
                        'post_type'  => 'product',
                        'post_title' => $title,
                    );

                    if ( !empty( $status ) )
                        $new_post[ 'post_status' ] = $status;

                    if ( !empty( $slug ) )
                        $new_post[ 'post_name' ] = sanitize_title( $slug );

                    if ( !empty( $description ) )
                        $new_post[ 'post_content' ] = $description;

                    if ( !empty( $shortdesc ) )
                        $new_post[ 'post_excerpt' ] = $shortdesc;

                    if ( !empty( $enable_reviews ) )
                        $new_post[ 'comment_status' ] = $enable_reviews;

                    if ( !empty( $date ) )
                        $new_post[ 'post_date' ] = $date;

                    $id             = wp_insert_post( $new_post );
                    $product        = new WC_Product( $id );
                    $is_new_product = true;
                } else {
                    // EDIT PRODUCT TYPE
                    if ( isset( $prod_type ) && $prod_type !== 'variation' ) {
                        wp_set_object_terms( $id, $prod_type, 'product_type' );
                    }

                    $product = wc_get_product( $id );
                }


                if ( $product ) {
                    $counter++;

                    $is_variation = false;
                    if ( $product->is_type( 'variation' ) || $prod_type == 'variation' ) {
                        $is_variation = true;
                    }

                    // EDIT REGULAR PRICE
                    if ( isset( $reg_price ) )
                        $product->set_regular_price( $reg_price );

                    // EDIT SALE PRICE
                    if ( isset( $sale_price ) )
                        $product->set_sale_price( $sale_price );

                    // EDIT SALE PRICE FROM
                    if ( isset( $sale_price_from ) ) {
                        $_date = !!$sale_price_from ? date( 'Y-m-d 00:00:00', strtotime( $sale_price_from ) ) : '';
                        $product->set_date_on_sale_from( $_date );
                    }

                    // EDIT SALE PRICE TO
                    if ( isset( $sale_price_to ) ) {
                        $price_change = true;
                        $_date        = !!$sale_price_to ? date( 'Y-m-d 23:59:59', strtotime( $sale_price_to ) ) : '';
                        $product->set_date_on_sale_to( $_date );
                    }

                    // EDIT POST
                    if ( !$is_new_product ) {
                        $this_post   = array(
                            'ID' => $id,
                        );
                        $post_change = false;

                        if ( !is_null( $date ) ) {
                            $post_change = true;
                            $post        = get_post( yit_get_base_product_id( $product ) );
                            $post_date   = $post->post_date;

                            if ( date( 'Y-m-d', strtotime( $post_date ) ) != date( 'Y-m-d', strtotime( $date ) ) )
                                $post_date = date( $date );

                            $this_post[ 'post_date' ]     = $post_date;
                            $this_post[ 'post_date_gmt' ] = gmdate( $post_date );
                        }

                        if ( !is_null( $title ) ) {
                            $post_change               = true;
                            $this_post[ 'post_title' ] = $title;
                        }

                        if ( !is_null( $enable_reviews ) ) {
                            $post_change                   = true;
                            $this_post[ 'comment_status' ] = $enable_reviews;
                        }

                        if ( !is_null( $status ) ) {
                            $post_change                = true;
                            $this_post[ 'post_status' ] = $status;
                        }

                        if ( !is_null( $slug ) ) {
                            $post_change              = true;
                            $this_post[ 'post_name' ] = $slug;
                        }

                        if ( !is_null( $menu_order ) && !$is_variation && !$product instanceof WC_Data ) {
                            $post_change               = true;
                            $this_post[ 'menu_order' ] = $menu_order;
                        }


                        if ( $post_change ) {
                            wp_update_post( $this_post );
                        }
                    }

                    // EDIT PRODUCT CATEGORIES
                    if ( isset( $categories ) && !$is_variation ) {
                        $terms = json_decode( $categories );
                        wp_set_post_terms( $id, $terms, 'product_cat' );
                    }

                    // EDIT PRODUCT TAGS
                    if ( isset( $tags ) && !$is_variation ) {
                        if ( is_taxonomy_hierarchical( 'product_tag' ) ) {
                            $tags_array = explode( ',', trim( $tags, " \n\t\r\0\x0B," ) );
                            $tags       = array();
                            if ( !!$tags_array ) {
                                foreach ( $tags_array as $current_tag ) {
                                    $term_name = trim( $current_tag );
                                    $term      = get_term_by( 'name', $term_name, 'product_tag' );
                                    if ( $term ) {
                                        $tags[] = $term->term_id;
                                    } else {
                                        $term = wp_insert_term( $term_name, 'product_tag' );
                                        if ( isset( $term[ 'term_id' ] ) ) {
                                            $tags[] = $term[ 'term_id' ];
                                        }
                                    }
                                }
                            }
                        }
                        wp_set_post_terms( $id, $tags, 'product_tag' );
                    }

                    if ( isset( $description ) ) {
                        $product->set_description( $description );
                    }

                    if ( isset( $shortdesc ) ) {
                        $product->set_short_description( $shortdesc );
                    }

                    // EDIT SKU
                    if ( isset( $sku ) ) {
                        try {
                            $product->set_sku( $sku );
                        } catch ( Exception $exception ) {
                            printf( 'Error %s: %s', $exception->getCode(), $exception->getMessage() );
                        }
                    }

                    // EDIT WEIGHT
                    if ( isset( $weight ) )
                        $product->set_weight( $weight );

                    // EDIT LENGHT
                    if ( isset( $length ) )
                        $product->set_length( $length );

                    // EDIT WIDTH
                    if ( isset( $width ) )
                        $product->set_width( $width );

                    // EDIT HEIGHT
                    if ( isset( $height ) )
                        $product->set_height( $height );

                    // EDIT PURCHASE NOTE
                    if ( isset( $purchase_note ) && !$is_variation )
                        $product->set_purchase_note( $purchase_note );

                    // EDIT PURCHASE NOTE
                    if ( isset( $download_limit ) )
                        $product->set_download_limit( $download_limit );

                    // EDIT PURCHASE NOTE
                    if ( isset( $download_expiry ) )
                        $product->set_download_expiry( $download_expiry );

                    // EDIT MENU ORDER
                    if ( isset( $menu_order ) && !$is_variation )
                        $product->set_menu_order( $menu_order );

                    // EDIT MANAGE STOCK
                    if ( isset( $manage_stock ) )
                        $product->set_manage_stock( $manage_stock );

                    // EDIT STOCK STATUS
                    if ( isset( $stock_status ) )
                        $product->set_stock_status( $stock_status );

                    // EDIT STOCK QUANTITY
                    if ( !$product->is_type( 'grouped' ) && isset( $stock_quantity ) )
                        $product->set_stock_quantity( wc_stock_amount( $stock_quantity ) );

                    // EDIT SOLD INDIVIDUALLY
                    if ( isset( $sold_individually ) && !$is_variation )
                        $product->set_sold_individually( $sold_individually );

                    // EDIT FEATURED
                    if ( isset( $featured ) && !$is_variation )
                        $product->set_featured( $featured );

                    // EDIT VIRTUAL
                    if ( isset( $virtual ) )
                        $product->set_virtual( $virtual );

                    // EDIT DOWNLOADABLE
                    if ( isset( $downloadable ) )
                        $product->set_downloadable( $downloadable );

                    // EDIT TAX STATUS
                    if ( isset( $tax_status ) && !$is_variation ) {
                        try {
                            $product->set_tax_status( $tax_status );
                        } catch ( Exception $exception ) {
                            printf( 'Error %s: %s', $exception->getCode(), $exception->getMessage() );
                        }
                    }

                    // EDIT TAX CLASS
                    if ( isset( $tax_class ) )
                        $product->set_tax_class( $tax_class );

                    // EDIT ALLOW BACKORDERS
                    if ( isset( $backorders ) )
                        $product->set_backorders( $backorders );

                    // EDIT SHIPPING CLASS
                    if ( isset( $shipping_class ) ) {
                        if ( $shipping_class > 0 ) {
                            $s = get_term_by( 'id', $shipping_class, 'product_shipping_class' );
                            wp_set_object_terms( $id, $s->name, 'product_shipping_class' );
                        } else {
                            wp_set_object_terms( $id, '', 'product_shipping_class' );
                        }
                    }

                    // EDIT VISIBILITY
                    if ( isset( $visibility ) && !$is_variation ) {
                        try {
                            $product->set_catalog_visibility( $visibility );
                        } catch ( Exception $exception ) {
                            printf( 'Error %s: %s', $exception->getCode(), $exception->getMessage() );
                        }
                    }

                    if ( $is_new_product && !isset( $visibility ) && !$is_variation ) {
                        try {
                            $product->set_catalog_visibility( 'visible' );
                        } catch ( Exception $exception ) {
                            printf( 'Error %s: %s', $exception->getCode(), $exception->getMessage() );
                        }
                    }

                    // EDIT BUTTON TEXT
                    if ( isset( $button_text ) && !$is_variation && $product instanceof WC_Product_External )
                        $product->set_button_text( $button_text );

                    // EDIT PRODUCT URL
                    if ( isset( $product_url ) && !$is_variation && $product instanceof WC_Product_External )
                        $product->set_product_url( $product_url );


                    // EDIT ATTRIBUTES
                    $attr_data          = array();
                    $var_attributes     = array();
                    $removed_attributes = array();
                    if ( count( $attributes_array ) > 0 ) {
                        foreach ( $attributes_array as $key => $value ) {
                            if ( !!$value ) {
                                if ( isset( $value[ 2 ] ) && is_array( $value[ 2 ] ) && count( $value[ 2 ] ) > 0 ) {
                                    $vals = array_map( 'intval', $value[ 2 ] );
                                } else {
                                    $vals                 = array();
                                    $removed_attributes[] = $key;
                                }

                                if ( !$is_variation ) {
                                    wp_set_object_terms( $id, $vals, $key );
                                } else {
                                    // VARIATIONS
                                    if ( isset( $vals[ 0 ] ) ) {
                                        $var_attributes[ $key ] = $vals[ 0 ];
                                    } else {
                                        $var_attributes[ $key ] = array();
                                    }
                                }

                                $attr_data[ $key ] = array(
                                    'name'         => $key,
                                    'is_visible'   => !!$value[ 0 ] ? $value[ 0 ] : 0,
                                    'is_variation' => !!$value[ 1 ] ? $value[ 1 ] : 0,
                                    'is_taxonomy'  => '1'
                                );

                                if ( $is_variation ) {
                                    $attr_data[ $key ][ 'value' ]        = $var_attributes[ $key ];
                                    $attr_data[ $key ][ 'is_variation' ] = 1;
                                } else {
                                    $attr_data[ $key ][ 'value' ] = $vals;
                                }
                            }
                        }
                    }

                    if ( count( $attr_data ) > 0 && !$is_variation ) {
                        $product_attributes = $product->get_attributes( 'edit' );

                        foreach ( $attr_data as $key => $value ) {
                            if ( in_array( $key, $removed_attributes ) && isset( $product_attributes[ $key ] ) ) {
                                unset( $product_attributes[ $key ] );
                            } else {
                                if ( isset( $product_attributes[ $key ] ) && $product_attributes[ $key ] instanceof WC_Product_Attribute ) {
                                    /** @var WC_Product_Attribute $current_attribute */
                                    $current_attribute = clone $product_attributes[ $key ];
                                } else {
                                    $current_attribute = new WC_Product_Attribute();
                                    $current_attribute->set_id( 1 );
                                }
                                $current_attribute->set_name( $value[ 'name' ] );
                                $current_attribute->set_visible( !!$value[ 'is_visible' ] );
                                $current_attribute->set_variation( !!$value[ 'is_variation' ] );
                                $current_attribute->set_options( $value[ 'value' ] );
                                $product_attributes[ $key ] = $current_attribute;
                            }
                        }
                        $product->set_attributes( $product_attributes );
                    }

                    if ( count( $var_attributes ) > 0 && $is_variation ) {
                        $product_attributes = $product->get_attributes( 'edit' );
                        foreach ( $var_attributes as $key => $value ) {
                            $attribute_term             = get_term_by( 'id', $value, $key );
                            $attribute_slug             = $attribute_term ? $attribute_term->slug : '';
                            $product_attributes[ $key ] = $attribute_slug;
                        }
                        $product->set_attributes( $product_attributes );
                    }

                    // UP SELLS
                    if ( isset( $upsells ) && !$is_variation )
                        $product->set_upsell_ids( $upsells );

                    // CROSS SELLS
                    if ( isset( $crosssells ) && !$is_variation )
                        $product->set_cross_sell_ids( $crosssells );

                    if ( isset( $image ) ) {
                        if ( !$is_variation ) {
                            if ( $image != '' ) {
                                set_post_thumbnail( $id, $image );
                            } else {
                                delete_post_thumbnail( $id );
                            }
                        } else {
                            if ( $image != '' ) {
                                update_post_meta( $id, '_thumbnail_id', absint( $image ) );
                            } else {
                                delete_post_meta( $id, '_thumbnail_id' );
                            }
                        }
                        $product->set_image_id( $image );
                    }

                    // IMAGE GALLERY
                    if ( isset( $image_gallery ) && !$is_variation ) {
                        $image_gallery = !is_array( $image_gallery ) ? explode( ',', $image_gallery ) : $image_gallery;
                        $product->set_gallery_image_ids( $image_gallery );
                    }

                    // DOWNLOADABLE FILES
                    if ( is_array( $downloadable_files ) ) {
                        $file_names = array();
                        $file_urls  = array();
                        $files      = array();

                        foreach ( $downloadable_files as $file ) {
                            $file_names[] = $file[ 0 ];
                            $file_urls[]  = trim( $file[ 1 ] );
                        }

                        $file_url_size      = sizeof( $file_urls );
                        $allowed_file_types = get_allowed_mime_types();

                        for ( $i = 0; $i < $file_url_size; $i++ ) {
                            if ( !empty( $file_urls[ $i ] ) ) {
                                // Find type and file URL
                                if ( 0 === strpos( $file_urls[ $i ], 'http' ) ) {
                                    $file_is  = 'absolute';
                                    $file_url = esc_url_raw( $file_urls[ $i ] );
                                } elseif ( '[' === substr( $file_urls[ $i ], 0, 1 ) && ']' === substr( $file_urls[ $i ], -1 ) ) {
                                    $file_is  = 'shortcode';
                                    $file_url = wc_clean( $file_urls[ $i ] );
                                } else {
                                    $file_is  = 'relative';
                                    $file_url = wc_clean( $file_urls[ $i ] );
                                }

                                $file_name = wc_clean( $file_names[ $i ] );
                                $file_hash = md5( $file_url );

                                // Validate the file extension
                                if ( in_array( $file_is, array( 'absolute', 'relative' ) ) ) {
                                    $file_type  = wp_check_filetype( strtok( $file_url, '?' ) );
                                    $parsed_url = parse_url( $file_url, PHP_URL_PATH );
                                    $extension  = pathinfo( $parsed_url, PATHINFO_EXTENSION );

                                    if ( !empty( $extension ) && !in_array( $file_type[ 'type' ], $allowed_file_types ) ) {
                                        echo sprintf( __( 'The downloadable file %s cannot be used as it does not have an allowed file type. Allowed types include: %s', 'woocommerce' ), '<code>' . basename( $file_url ) . '</code>', '<code>' . implode( ', ', array_keys( $allowed_file_types ) ) . '</code>' );
                                        continue;
                                    }
                                }

                                // Validate the file exists
                                if ( 'relative' === $file_is && !apply_filters( 'woocommerce_downloadable_file_exists', file_exists( $file_url ), $file_url ) ) {
                                    echo sprintf( __( 'The downloadable file %s cannot be used as it does not exist on the server.', 'woocommerce' ), '<code>' . $file_url . '</code>' );
                                    continue;
                                }

                                $files[ $file_hash ] = array(
                                    'name' => $file_name,
                                    'file' => $file_url
                                );
                            }
                        }
                        $product->set_downloads( $files );
                    } else if ( $downloadable_files === '' ) {
                        $product->set_downloads( array() );
                    }

                    // SYNC FOR VARIATIONS
                    $prod_id = $id;
                    if ( $is_variation ) {
                        $parent_id = $product->get_parent_id();
                        WC_Product_Variable::sync( $parent_id );
                        $prod_id = $parent_id;
                    }

                    wc_delete_product_transients( $prod_id );

                    do_action( 'yith_wcbep_update_product', $product, $keys, $row, $is_variation );

                    /**
                     * WPML Compatilbility
                     * I changed the pagenow to post.php to use
                     * standard WPML method WCML_Products->sync_post_action
                     */
                    $old_pagenow = $pagenow;
                    $pagenow     = 'post.php';
                    $post        = get_post( yit_get_base_product_id( $product ) );

                    $product->save();

                    do_action( 'save_post', $prod_id, $post, !$is_new_product );

                    $pagenow = $old_pagenow;
                    /* ------------------------------- */
                }

                do_action( 'yith_wcbep_after_bulk_edit_product', compact( 'keys', 'row', 'edited', 'to_edit' ) );
            }
            die();
        }

        public function admin_enqueue_scripts() {
            parent::admin_enqueue_scripts();

            $suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            $screen   = get_current_screen();
            $is_panel = strpos( $screen->id, '_page_yith_wcbep_panel' ) > -1;
            if ( $is_panel ) {
                wp_enqueue_script( 'yith_wcbep_enabled_columns_tab_js', YITH_WCBEP_ASSETS_URL . '/js/enabled_columns_tab' . $suffix . '.js', array( 'jquery' ), YITH_WCBEP_VERSION, true );
                wp_enqueue_script( 'yith_wcbep_custom_fields_tab_js', YITH_WCBEP_ASSETS_URL . '/js/custom_fields_tab' . $suffix . '.js', array( 'jquery' ), YITH_WCBEP_VERSION, true );
            }
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since 2.0.0
         */
        public function register_plugin_for_activation() {
            if ( function_exists( 'YIT_Plugin_Licence' ) ) {
                YIT_Plugin_Licence()->register( YITH_WCBEP_INIT, YITH_WCBEP_SECRET_KEY, YITH_WCBEP_SLUG );
            }
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since 2.0.0
         */
        public function register_plugin_for_updates() {
            if ( function_exists( 'YIT_Upgrade' ) ) {
                YIT_Upgrade()->register( YITH_WCBEP_SLUG, YITH_WCBEP_INIT );
            }

        }
    }
}

/**
 * Unique access to instance of YITH_WCBEP_Admin_Premium class
 *
 * @return YITH_WCBEP_Admin_Premium
 * @deprecated since 1.2.1 use YITH_WCBEP_Admin() instead
 * @since      1.0.0
 */
function YITH_WCBEP_Admin_Premium() {
    return YITH_WCBEP_Admin();
}