<?php

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Warranty_Active_Reports_List_Table extends WP_List_Table {

    public $valid_orders = array();

    function __construct( $args = array() ) {
        parent::__construct($args);
    }

    function get_columns(){
        $columns = array(
            'order_id'  => __('Order ID', 'wc_warranty'),
            'status'    => __('RMA Status', 'wc_warranty'),
            'customer'  => __('Customer Name', 'wc_warranty'),
            'product'   => __('Product', 'wc_warranty'),
            'validity'  => __('Validity', 'wc_warranty'),
            'date'      => __('Order Date', 'wc_warranty')
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'order_id'  => array('order_id',false),
            'date'      => array('date',false)
        );
        return $sortable_columns;
    }

    function prepare_items() {
        global $wpdb;

        $columns    = $this->get_columns();
        $hidden     = array();

        $sortable   = array();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $per_page = 10;
        $completed_status = warranty_get_completed_status();

        $args = array(
            'post_type'     => 'warranty_request',
            'per_page'      => $per_page,
            'paged'         => $this->get_pagenum(),
            'tax_query'     => array(
                array(
                    'taxonomy'  => 'shop_warranty_status',
                    'field'     => 'slug',
                    'terms'     => $completed_status->slug,
                    'operator'  => 'NOT IN'
                )
            )
        );

        if ( isset($_GET['s']) && !empty($_GET['s']) ) {
            $args['meta_query'][] = array(
                'key'       => '_order_id',
                'value'     => $_GET['s'],
                'compare'   => 'LIKE'
            );
        }

        $query = new WP_Query( $args );

        $warranties = array();
        foreach ( $query->posts as $post ) {
            $order_id = get_post_meta( $post->ID, '_order_id', true );
            $order = wc_get_order( $order_id );

            if ( ! $order ) {
                continue;
            }

            $warranties[] = $post;
        }

        $this->items    = $warranties;
        $total_items    = count( $warranties );

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page'    => $per_page
        ) );

        wp_reset_postdata();
    }

    function column_order_id($item) {

        $order_id = get_post_meta( $item->ID, '_order_id', true );
        $order = wc_get_order( $order_id );
        $order_number = ( $order ) ? $order->get_order_number() : '-';

        if ( class_exists('WC_Seq_Order_Number') ) {
            $seq_order_id = $GLOBALS['wc_seq_order_number']->find_order_by_order_number( $order_id );

            if ( $seq_order_id ) {
                return '<a href="post.php?post='. $order_id .'&action=edit">#'. $seq_order_id .'</a>';
            } else {
                return '<a href="post.php?post='. $order_id .'&action=edit">'. $order_number .'</a>';
            }
        } else {
            return '<a href="post.php?post='. $order_id .'&action=edit">'. $order_number .'</a>';
        }

    }

    function column_status($item) {
        $term       = wp_get_post_terms( $item->ID, 'shop_warranty_status' );
        $status     = isset($term[0]) ? $term[0]->name : '-';
        return $status;
    }

    function column_customer($item) {
        $order_id = get_post_meta( $item->ID, '_order_id', true );
        $order = wc_get_order($order_id);
        $first_name = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_first_name' );
        $last_name = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_last_name' );

        return $first_name .' '. $last_name;
    }

    function column_product($item) {
	    $products = warranty_get_request_items( $item->ID );

	    $out = '';

	    foreach ( $products as $product ) {
		    if ( empty( $product['product_id'] ) && empty( $item['product_name'] ) ) {
			    continue;
		    }

		    if ( $product['product_id'] == 0 ) {
			    $out .= $item['product_name'] .'<br/>';
		    } else {
			    $title = warranty_get_product_title( $product['product_id'] );
			    $out .= '<a href="post.php?post='. $product['product_id'] .'&action=edit">'. $title .'</a> &times; '. $product['quantity'] .'<br/>';
		    }
	    }

	    return $out;
    }

    function column_validity($item) {
        $order_id       = get_post_meta( $item->ID, '_order_id', true );
        $order          = wc_get_order( $order_id );
        $item_key       = get_post_meta( $item->ID, '_index', true );
        $warranty       = wc_get_order_item_meta( $item_key, '_item_warranty', true );
        $warranty       = maybe_unserialize($warranty);
        $addon_index    = wc_get_order_item_meta( $item_key, '_item_warranty_selected', true );

        if ( $warranty ) {
            // order's date of completion must be within the warranty period
            if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
                $completed = get_post_meta( $order->id, '_completed_date', true);
            } else {
                $completed = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;
            }

            if ( 'addon_warranty' === $warranty['type'] ) {
                if (! empty( $completed ) ) {
                    $addon          = $warranty['addons'][$addon_index];
                    $date           = warranty_get_date( $completed, $addon['value'], $addon['duration'] );

                    echo $date;
                }

            } elseif ( $warranty['type'] == 'included_warranty' ) {
                if ( $warranty['length'] == 'lifetime' ) {
                    echo __('Lifetime', 'wc_warranty');
                } else {
                    if (! empty($completed) ) {
                        $date   = warranty_get_date( $completed, $warranty['value'], $warranty['duration'] );

                        echo $date;
                    }
                }
            }
        }
    }

    function column_date($item) {
        $order_id   = get_post_meta( $item->ID, '_order_id', true );
        $order      = wc_get_order( $order_id );
        return date_i18n( get_option('date_format') .' '. get_option('time_format'), strtotime( WC_Warranty_Compatibility::get_order_prop( $order, 'modified_date' ) ) );
    }

    function no_items() {
        _e( 'No requests found.', 'wc_warranty' );
    }

}

echo '<style type="text/css">
table.woocommerce_page_warranty_requests #status { width: 200px; }
.wc-updated {width: 95%; margin: 5px 0 15px; background-color: #ffffe0; border-color: #e6db55; padding: 0 .6em; -webkit-border-radius: 3px; border-radius: 3px; border-width: 1px; border-style: solid;}
.wc-updated p {margin: .5em 0 !important; padding: 2px;}
</style>';

if ( isset($_GET['updated']) ) {
    echo '<div class="updated"><p>'. $_GET['updated'] .'</p></div>';
}
$active_table = new Warranty_Active_Reports_List_Table();
$active_table->prepare_items();
?>

<form action="admin.php" method="get" style="margin-top: 20px;">
    <input type="hidden" name="page" value="warranties-reports" />

    <p class="search-box">
        <label class="screen-reader-text" for="search"><?php _e('Search', 'wc_warranty') ?>:</label>
        <input type="search" id="search" name="s" value="<?php _admin_search_query(); ?>" placeholder="Order #" />
        <?php submit_button( __('Search', 'wc_warranty'), 'button', false, false, array('id' => 'search-submit') ); ?>
    </p>
</form>

<?php
$active_table->display();
