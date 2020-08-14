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
                'linked'    => __( 'Linked products', 'yith-woocommerce-frequently-bought-together' ),
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
                    $product_url        = get_edit_post_link( yit_get_base_product_id( $product ) );
					$product_name		= sprintf( '<strong><a class="tips" target="_blank" href="%s" data-tip="%s">%s</a></strong>', esc_url( $product_url ), __( 'Edit product', 'yith-woocommerce-waiting-list' ), $product->get_title() );

                    return '<div class="product-image">' . $product->get_image( 'thumbnail' ) . '</div>' . '<div class="product-name">' . $product_name . '</div>';
                    break;

                case 'linked' :

                	$return_html = '';
                	foreach ( $rec['products'] as $product_id ) {
                		$product = wc_get_product( $product_id );
                		if( ! $product ) {
                			continue;
						}

                		$edit_url 		= get_edit_post_link( yit_get_base_product_id( $product ) );
                		$return_html 	.= sprintf( '<div class="linked-product" data-product_id="%d" data-linked_id="%d"><strong><a href="%s" target="_blank">%s</a><strong></div>', intval( $rec['product_id'] ), intval( $product_id ), $edit_url, $product->get_title() );
					}

                    return $return_html;
                    break;

                case 'actions':

                    $delete_query_args = array(
                        'page'   => $_GET['page'],
                        'tab'    => $_GET['tab'],
                        'action' => 'delete',
                        'id'     => $rec['product_id']
                    );
                    $delete_url        = add_query_arg( $delete_query_args, admin_url( 'admin.php' ) );
                    return '<a href="' . esc_url( $delete_url ) . '" class="button">' . __( 'Delete All', 'yith-woocommerce-frequently-bought-together' ) . '</a>';
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