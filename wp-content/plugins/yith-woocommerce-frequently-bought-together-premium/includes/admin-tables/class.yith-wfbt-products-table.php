<?php
if ( ! defined( 'YITH_WFBT' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists( 'YITH_WFBT_Products_Table' ) ) {
	/**
	 * Products list table
	 *
	 * @class   YITH_WFBT_Products_Table
	 * @package YITH Woocommerce Frequently Bought Togheter Premium
	 * @since   1.0.0
	 * @author  Yithemes
	 *
	 */
	class YITH_WFBT_Products_Table extends WP_List_Table {

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
                'cb'        => '<input type="checkbox" />',
                'product'   => __( 'Product', 'yith-woocommerce-frequently-bought-together' ),
                'thumb'     => __( 'Thumbnail', 'yith-woocommerce-frequently-bought-together' ),
                'linked'    => __( 'Amount of linked products', 'yith-woocommerce-frequently-bought-together' ),
                'actions'   => __( 'Actions', 'yith-woocommerce-frequently-bought-together' )
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

            $product = wc_get_product( intval( $rec['product_id'] ) );
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

                case 'thumb' :
                    return $product->get_image();
                    break;

                case 'linked' :

                    $view_query_args = array(
                        'page'      => $_GET['page'],
                        'tab'       => $_GET['tab'],
                        'view'      => 'linked',
                        'post_id'   => $rec['product_id']
                    );
                    $view_url   = add_query_arg( $view_query_args, admin_url( 'admin.php' ) );

                    return '<a href="' . esc_url( $view_url ) . '">' . count( $rec['products'] ) . '</a>';
                    break;

                case 'actions':

                    $delete_query_args = array(
                        'page'   => $_GET['page'],
                        'tab'    => $_GET['tab'],
                        'action' => 'delete',
                        'id'     => $rec['product_id']
                    );
                    $delete_url        = add_query_arg( $delete_query_args, admin_url( 'admin.php' ) );
                    $actions_button    = '<a href="' . esc_url( $delete_url ) . '" class="button">' . __( 'Delete All', 'yith-woocommerce-frequently-bought-together' ) . '</a>';

                    $view_query_args = array(
                        'page'      => $_GET['page'],
                        'tab'       => $_GET['tab'],
                        'view'      => 'linked',
                        'post_id'   => $rec['product_id']
                    );
                    $view_url        = add_query_arg( $view_query_args, admin_url( 'admin.php' ) );
                    $actions_button .= '<a href="' . esc_url( $view_url ) . '" class="button">' . __( 'View Linked', 'yith-woocommerce-frequently-bought-together' ) . '</a>';

                    return $actions_button;
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
            return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $rec['product_id'] );
        }

        /**
         * Sets bulk actions for table
         *
         * @return array Array of available actions
         * @since 1.3.0
         */
        public function get_bulk_actions() {
            $actions = array(
                'delete' => __( 'Delete', 'yith-woocommerce-frequently-bought-together' )
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

            // query parts initializating
            $where      = $wpdb->prepare( "pm.meta_key = '%s' AND pm.meta_value NOT LIKE 'a:0:{}'", YITH_WFBT_META );
            // Search
            if ( $q['s'] ) {
                // added slashes screw with quote grouping when done early, so done later
                $q['s'] = stripslashes( $q['s'] );
                // there are no line breaks in <input /> fields
                $q['s'] = str_replace( array( "\r", "\n" ), '', $q['s'] );

                $s = $wpdb->prepare( "p.post_title LIKE %s", "%{$q['s']}%" );
                $where .= " AND {$s}";
            }

            $where  = apply_filters( 'yith_wfbt_linked_products_where', $where );

            $paged  = isset( $_GET['paged'] ) ? $q['number'] * ( intval( $_GET['paged'] ) - 1 ) : 0;
            $items  = $wpdb->get_results( "SELECT p.ID AS product_id, pm.meta_value AS data FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id WHERE $where", ARRAY_A );

            // unserialize it
            $unserialized_items = array();
            $total_items        = 0;

            foreach( $items as $item ) {

                $data   = maybe_unserialize( $item['data'] );
                $group  = isset( $data['products'] ) ? $data['products'] : array();
                if( empty( $group ) ) {
                    continue;
                }

                $unserialized_items[] = array(
                    'product_id'    => intval( $item['product_id'] ),
                    'products'      => $group
                );
                ++$total_items;
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
            _e( 'No items found.', 'yith-woocommerce-frequently-bought-together' );
        }
	}
}