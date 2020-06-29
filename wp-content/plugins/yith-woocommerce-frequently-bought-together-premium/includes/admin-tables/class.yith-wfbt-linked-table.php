<?php
if ( ! defined( 'YITH_WFBT' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists( 'YITH_WFBT_Linked_Table' ) ) {
	/**
	 * Products list table
	 *
	 * @class   YITH_WFBT_Linked_Table
	 * @package YITH Woocommerce Frequently Bought Togheter Premium
	 * @since   1.0.0
	 * @author  Yithemes
	 *
	 */
	class YITH_WFBT_Linked_Table extends WP_List_Table {

        /**
         * Construct
         */
        public function __construct() {

            //Set parent defaults
            parent::__construct( array(
                    'singular' => 'product', //singular name of the listed records
                    'plural'   => 'products', //plural name of the listed records
                    'ajax'     => false //does this table support ajax?
                )
            );
        }

        /**
         * Returns columns available in table
         *
         * @return array Array of columns of the table
         * @since 1.1.3
         */
        public function get_columns() {
            $columns = array(
                'cb'            => '<input type="checkbox" />',
                'product'       => __( 'Product', 'yith-woocommerce-frequently-bought-together' ),
                'variation'     => __( 'Variation', 'yith-woocommerce-frequently-bought-together' ),
                'thumb'         => __( 'Thumbnail', 'yith-woocommerce-frequently-bought-together' ),
                'status'        => __( 'Stock Status', 'yith-woocommerce-frequently-bought-together' ),
                'actions'       => __( 'Actions', 'yith-woocommerce-frequently-bought-together' ),
            );

            return $columns;
        }

        /**
         * Print the columns information
         *
         * @param $rec
         * @param $column_name
         *
         * @return string
         * @since 1.1.3
         */
        public function column_default( $rec, $column_name ) {

            $product = wc_get_product( intval( $rec ) );
            if( ! $product ) {
                return null;
            }

            /** @var WC_Product $product */
            switch ( $column_name ) {

                case 'product':
                    $product_query_args = array(
                        'post'   => yit_get_base_product_id( $product ),
                        'action' => 'edit'
                    );
                    $product_url        = add_query_arg( $product_query_args, admin_url( 'post.php' ) );

                    return sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">%s</a></strong>', esc_url( $product_url ), __( 'Edit product', 'yith-woocommerce-waiting-list' ), $product->get_title() );
                    break;

                case 'variation' :
                    /**
                     * @type $product WC_Product
                     */
                    if( $product->is_type( 'variation' ) ) {

                        $variations = $product->get_variation_attributes();

                        $html = '<ul>';

                        foreach( $variations as $key => $value ) {
                            $key = ucfirst( str_replace( 'attribute_pa_' , '', $key ) );
                            $html .= '<li>' . $key . ': ' . $value . '</li>';
                        }

                        $html .= '</ul>';

                        echo $html;
                    }
                    else {
                        echo '-';
                    }
                    break;
                case 'thumb' :
                    return $product->get_image();
                    break;

                case 'status' :

                    /**
                     * @type $product WC_Product
                     */
                    $status = $product->get_availability();

                    return ( $status['availability'] != '' ) ? '<span class="' . $status['class'] . '">' . $status['availability'] . '</span>' : ' - ';
                    break;

                case 'actions':

                    $delete_query_args = array(
                        'page'      => $_GET['page'],
                        'tab'       => $_GET['tab'],
                        'view'      => 'linked',
                        'action'    => 'remove_linked',
                        'post_id'   => $_GET['post_id'],
                        'id'        => $rec
                    );
                    $delete_url        = add_query_arg( $delete_query_args, admin_url( 'admin.php' ) );

                    return '<a href="' . esc_url( $delete_url ) . '" class="button">' . __( 'Delete', 'yith-woocommerce-frequently-bought-together' ) . '</a>';
                    break;
            }

            return null;
        }

        /**
         * Prints column cb
         *
         * @param $rec Object Item to use to print CB record
         *
         * @return string
         * @since 1.1.3
         */
        public function column_cb( $rec ) {
            return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $rec );
        }

        /**
         * Sets bulk actions for table
         *
         * @return array Array of available actions
         * @since 1.3.0
         */
        public function get_bulk_actions() {
            $actions = array(
                'remove_linked' => __( 'Delete', 'yith-woocommerce-frequently-bought-together' )
            );

            return apply_filters( 'yith-wfbt-table-products-bulk-actions', $actions );
        }

        /**
         * Prepare items for table
         *
         * @param array $args
         * @since 1.3.0
         */
        public function prepare_items( $args = array() ) {

            // blacklist args
            $q = wp_parse_args( $args, array(
                'paged'             => absint( $this->get_pagenum() ),
                'number'            => 20,
                's'                 => isset( $_REQUEST['s'] ) ? $_REQUEST['s'] : ''
            ) );

            global $wpdb;

            $product_id = isset( $_GET['post_id'] ) ? $_GET['post_id'] : 0;
            $item       = '';
            if( $product_id ) {
                // query
                $where  = $wpdb->prepare( "pm.meta_key = '%s' AND pm.post_id = '%d'", array( YITH_WFBT_META, $product_id ) );
                $paged  = isset( $_GET['paged'] ) ? $q['number'] * ( intval( $_GET['paged'] ) - 1 ) : 0;
                $item   = $wpdb->get_var( "SELECT pm.meta_value FROM {$wpdb->postmeta} AS pm WHERE $where" );
                is_null( $item ) && $item = '';
            }

            $item               = maybe_unserialize( $item );
            $unserialized_items = array();
            $total_items        = 0;

            if( ! empty( $item['products'] ) ) {
                foreach( $item['products'] as $product_id ) {

                    if( $q['s'] ) {
                        $product    = wc_get_product( intval( $product_id ) );
                        if( ! $product ) {
                            continue;
                        }
                        $title      = strtolower( $product->get_title() );
                        $search     = str_replace( array( "\r", "\n" ), '', $q['s'] );
                        $q['s']     = stripslashes( strtolower( $q['s'] ) );
                        if( FALSE === strpos( $title, $search ) ) {
                            continue;
                        }
                    }

                    $unserialized_items[] = $product_id;
                    ++$total_items;
                }
            }

            // sets columns headers
            $columns               = $this->get_columns();
            $this->_column_headers = array( $columns, array(), array() );

            // retrieve data for table. Slice array for pagination
            $this->items = array_slice( $unserialized_items, $paged, $q['number'] );

            // sets pagination args
            if ( ! empty( $q['number'] ) ) {
                $this->set_pagination_args(
                    array(
                        'total_items' => $total_items,
                        'per_page'    => $q['number'],
                        'total_pages' => ceil( $total_items / $q['number'] )
                    )
                );
            }
        }

        /**
         * Display the search box.
         *
         * @since 3.1.0
         * @access public
         *
         * @param string $text     The search button text
         * @param string $input_id The search input id
         */
        public function add_search_box( $text, $input_id ) {
            parent::search_box( $text, $input_id );
        }

        /**
         * Message to be displayed when there are no items
         *
         * @since 3.1.0
         * @access public
         */
        public function no_items() {
            esc_html_e( 'No items found.', 'yith-woocommerce-frequently-bought-together' );
        }
	}
}