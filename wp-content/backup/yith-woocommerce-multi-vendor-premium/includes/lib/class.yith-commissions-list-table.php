<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}


if ( ! class_exists( 'YITH_Commissions_List_Table' ) ) {
    /**
     *
     *
     * @class class.yith-commissions-list-table
     * @package    Yithemes
     * @since      Version 1.0.0
     * @author     Your Inspiration Themes
     *
     */
    class YITH_Commissions_List_Table extends WP_List_Table {

        /**
         * Vendor object
         *
         * @var array
         * @since 1.0
         */
        protected $_vendor;

        /**
         * Construct
         */
        public function __construct() {

            //Set parent defaults
            parent::__construct( array(
                    'singular' => 'commission', //singular name of the listed records
                    'plural'   => 'commissions', //plural name of the listed records
                    'ajax'     => false //does this table support ajax?
                )
            );

            $this->_vendor = yith_get_vendor( 'current', 'user' );
        }

        /**
         * Returns columns available in table
         *
         * @return array Array of columns of the table
         * @since 1.0.0
         */
        public function get_columns() {
            $columns = apply_filters( 'yith_commissions_list_table_column', array(
                    'commission_id'     => __( 'ID', 'yith-woocommerce-product-vendors' ),
                    'commission_status' => '<span class="status_head tips" data-tip="' . esc_attr__( 'Status', 'yith-woocommerce-product-vendors' ) . '">' . esc_attr__( 'Status', 'yith-woocommerce-product-vendors' ) . '</span>',
                    'order_id'          => __( 'Order', 'yith-woocommerce-product-vendors' ),
                    'line_item'         => __( 'Product', 'yith-woocommerce-product-vendors' ),
                    'rate'              => __( 'Rate', 'yith-woocommerce-product-vendors' ),
                    'amount'            => __( 'Total', 'yith-woocommerce-product-vendors' ),
                    'refunded'          => __( 'Refunded', 'yith-woocommerce-product-vendors' ),
                    'to_pay'            => __( 'To pay', 'yith-woocommerce-product-vendors' ),
                    'user'              => __( 'User', 'yith-woocommerce-product-vendors' ),
                    'vendor'            => YITH_Vendors()->get_vendors_taxonomy_label( 'singular_name' ),
                    'bank_account'      => __( 'IBAN/BIC', 'yith-woocommerce-product-vendors' ),
                    'date'              => __( 'Date', 'yith-woocommerce-product-vendors' ),
                    'date_edit'         => __( 'Last update', 'yith-woocommerce-product-vendors' ),
                    'user_actions'      => __( 'Actions', 'yith-woocommerce-product-vendors' ),
                )
            );

            if ( $this->_vendor->is_valid() && $this->_vendor->has_limited_access() && $this->_vendor->is_owner() ) {
                $to_remove = apply_filters( 'yith_columns_to_remove', array( 'user', 'vendor', 'cb' ) );
                foreach ( $to_remove as $remove ) {
                    unset( $columns[$remove] );
                }
            }

            return $columns;
        }

        /**
         * Prepare items for table
         *
         * @return void
         * @since 1.0.0
         */
        public function prepare_items() {

            $items = array();

            // sets pagination arguments
            $per_page     = $this->get_items_per_page( 'edit_commissions_per_page' );
            $current_page = absint( $this->get_pagenum() );

            // commissions args
            $args = array(
                'status'  => $this->get_current_view(),
                'paged'   => $current_page,
                'number'  => $per_page,
                'orderby' => 'ID',
	            'order'   => 'DESC',
            );

            if( ! empty( $_GET['orderby'] ) ){
                $args['orderby'] = $_GET['orderby'];
            }

            if( ! empty( $_GET['order'] ) ){
                $args['order'] = $_GET['order'];
            }

	        // merge Unpaid with Processing
	        if ( 'unpaid' == $args['status'] ) {
		        $args['status'] = array( 'unpaid', 'processing' );
	        }

            if ( $this->_vendor->is_valid() && $this->_vendor->has_limited_access() && $this->_vendor->is_owner() ) {
                $args['user_id'] = get_current_user_id();
            }

            if( ! empty( $_GET['vendor_id'] ) ){
                $args['vendor_id'] = $_GET['vendor_id'];
            }

            if( ! empty( $_GET['product_id'] ) ){
                $args['product_id'] = $_GET['product_id'];
            }

	        if( ! empty( $_GET['m'] ) ){
		        $args['m'] = $_GET['m'];
	        }

	        if( ! empty( $_GET['s'] ) ){
		        $args['s'] = $_GET['s'];
	        }

            $args = apply_filters( 'yith_wpv_commissions_table_args', $args );

            $commission_ids = YITH_Commissions()->get_commissions( $args );
            $total_items    = YITH_Commissions()->count_commissions( 'last-query' );

            // sets columns headers
            $columns               = $this->get_columns();
            $hidden                = array();
            $sortable              = $this->get_sortable_columns();
            $this->_column_headers = array( $columns, $hidden, $sortable );

            foreach ( $commission_ids as $commission_id ) {
                $items[] = YITH_Commission( $commission_id );
            }

            // retrieve data for table
            $this->items = $items;

            // sets pagination args
            $this->set_pagination_args( array(
                    'total_items' => $total_items,
                    'per_page'    => $per_page,
                    'total_pages' => ceil( $total_items / $per_page )
                )
            );
        }

	    /**
	     * Display the search box.
	     *
	     * @since 3.1.0
	     * @access public
	     *
	     * @param string $text The search button text
	     * @param string $input_id The search input id
	     *
	     * @return bool
	     */
        public function add_search_box( $text, $input_id ) {
            return false;
        }

        /**
         * Decide which columns to activate the sorting functionality on
         * @return array $sortable, the array of columns that can be sorted by the user
         */
        public function get_sortable_columns() {
            return array(
                'commission_id' => array( 'ID', false ),
                'order_id'      => array( 'order_id', false ),
                'amount'        => array( 'amount', false ),
                'date_edit'     => array( 'last_edit', false ),
                'vendor'        => array( 'vendor_id', false ),
            );
        }

        /**
         * Sets bulk actions for table
         *
         * @return array Array of available actions
         * @since 1.0.0
         */
        public function get_bulk_actions() {
            return array();
        }

        /**
         * Print the columns information
         *
         * @param $rec  \YITH_Commission
         * @param $column_name
         *
         * @return string
         */
        public function column_default( $rec, $column_name ) {
            switch ( $column_name ) {

                case 'commission_id':
                    $order = wc_get_order( $rec->order_id );
                    $commission_url = apply_filters( 'yith_wcmv_commissions_list_table_commission_url', $rec->get_view_url( 'admin' ), $rec );
                    $order ? printf( '<a href="%s"><strong>#%d</strong></a>', $commission_url, $rec->id ) : printf( '<strong>#%d</strong>', $rec->id );
                    break;

                case 'commission_status':
                    $display = $rec->get_status( 'display' );
                    return "<mark data-tip='{$display}' class='{$rec->status} tips'>{$display}</mark>";
                    break;

                case 'order_id':
                    /** @var WC_Order $order */
                    $order = wc_get_order( $rec->order_id );

                    if( ! $order ){
                        echo '<small class="meta">' . __( 'Order Deleted', 'yith-woocommerce-product-vendors' ) . '</small>';
                        return;
                    }

                    if ( $order->get_user_id() ) {
                        $user_info = $order->get_user();
                    }

                    if ( ! empty( $user_info ) ) {

                        $current_user_can = apply_filters( 'yith_wcmv_commissions_list_table_can_edit_users', current_user_can( 'edit_users' ) || get_current_user_id() == $user_info->ID );

                        $username = $current_user_can ? '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">' : '';

                        if ( $user_info->first_name || $user_info->last_name ) {
                            $username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
                        }
                        else {
                            $username .= esc_html( ucfirst( $user_info->display_name ) );
                        }

                        if ( $current_user_can ) {
                            $username .= '</a>';
                        }

                    }
                    else {
                        $billing_first_name = yit_get_prop( $order, 'billing_first_name', true );
                        $billing_last_name  = yit_get_prop( $order, 'billing_last_name', true );
                        if ( $billing_first_name || $billing_last_name ) {
                            $username = trim( $billing_first_name . ' ' . $billing_last_name );
                        }
                        else {
                            $username = __( 'Guest', 'yith-woocommerce-product-vendors' );
                        }
                    }

	                $order_id       = yit_get_prop( $order, 'id', true );
	                $order_number   = '<strong>#' . esc_attr( $order->get_order_number() ) . '</strong>';
	                $order_uri      = apply_filters( 'yith_wcmv_commissions_list_table_order_url', admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ), $rec, $order );
	                $order_uri_html = '<a href="' . $order_uri . '">' . $order_number . '</a>';
	                $order_info     = $this->_vendor->is_super_user() ? $order_uri : apply_filters( 'yith_wcmv_commissions_order_column', $order_number, $order->get_order_number(), $rec );

                    if( $this->_vendor->is_super_user() ){
                        $order_info = $order_uri_html;
                    }

                    else if( 'yes' == get_option( 'yith_wpv_vendors_option_order_management', 'no' ) && defined( 'YITH_WPV_PREMIUM' ) && YITH_WPV_PREMIUM && $this->_vendor->has_limited_access() && wp_get_post_parent_id( $order_id )&& in_array( $order_id, $this->_vendor->get_orders() ) ){
                        $order_info = $order_uri_html;
                    }

                    else {
                        $order_info = $order_number;
                    }

                    printf( _x( '%s by %s', 'Order number by user', 'yith-woocommerce-product-vendors' ), $order_info, $username );

                    $billing_email = yit_get_prop( $order, 'billing_email', true );

                    if ( $billing_email ) {
                        echo '<small class="meta email"><a href="' . esc_url( 'mailto:' . $billing_email ) . '">' . esc_html( $billing_email ) . '</a></small>';
                    }

                    do_action( 'yith_wpv_after_order_column', $order );
                    break;

                case 'line_item':
                    $to_return = '<small class="meta">-</small>';
                    if( 'shipping' == $rec->type ){
                        $shipping_fee = _x( 'Shipping fee', '[admin]: commission type', 'yith-woocommerce-product-vendors' );
                        $to_return = "<strong>{$shipping_fee}</strong>";
                    }

                    else {
                        $product = $rec->get_item();
                        $order   = $rec->get_order();

                        if( $product ){
                            $product_url = apply_filters( 'yith_wcmv_commissions_list_table_product_url', get_edit_post_link( $product['product_id'] ), $product, $rec );
                            $to_return = ! empty( $product_url ) ? "<a target='_blank' href='{$product_url}'><strong>{$product['name']}</strong></a>" : "<strong>{$product['name']}</strong>";
	                        $commission_included_tax = 'yes' == wc_get_order_item_meta( $product->get_id(), '_commission_included_tax', true );
                            $product_price = wc_price( $order->get_item_total( $product, $commission_included_tax, true ), array( 'currency' => $order->get_currency() ) );
                            $to_return .= sprintf( ' (%s)', $product_price );
                        }
                    }
                    return $to_return;
                    break;

                case 'rate':
                    return $rec->rate * 100 . '%';
                    break;

                case 'user':
                    $user      = $rec->get_user();

                    if ( empty( $user ) ) {
                        return "<em>" . __( 'User deleted', 'yith-woocommerce-product-vendors' ) . "</em>";
                    }

                    $user_url  = get_edit_user_link( $rec->user_id );
                    $user_name = $user->display_name;
                    return ! empty( $user_url ) ? "<a href='{$user_url}' target='_blank'>{$user_name}</a>" : $user_name;
                    break;

                case 'vendor':
	                $vendor_url = $vendor_name = '';
                    $vendor = $rec->get_vendor();

                    if( ! empty( $vendor ) ){
	                    if ( ! $vendor->is_valid() ) {
		                    return "<em>" . __( 'Vendor deleted', 'yith-woocommerce-product-vendors' ) . "</em>";
	                    }

	                    $vendor_url  = apply_filters( 'yith_wcmv_commissions_list_table_vendor_url', get_edit_term_link( $vendor->id, $vendor->taxonomy ), $vendor, $rec );
	                    $vendor_name = $vendor->name;
                    }

                    return ! empty( $vendor_url ) ? "<a href='{$vendor_url}' target='_blank'>{$vendor_name}</a>" : $vendor_name;
                    break;

                case 'amount':
                    /** @var WC_Order $order */
                    $order = wc_get_order( $rec->order_id );
                    $args = array();

                    if( $order instanceof WC_Order ){
                        $args['currency'] = yith_wcmv_get_order_currency( $order );
                    }
                    return $rec->get_amount('display', $args );
                    break;

                case 'user_actions':
                    $details_url = apply_filters( 'yith_wcmv_commissions_list_table_commission_url', $rec->get_view_url( 'admin' ), $rec );
                    printf( '<a class="button tips view" href="%1$s" data-tip="%2$s">%2$s</a>', $details_url, __( 'View', 'yith-woocommerce-product-vendors' ) );
	                do_action( 'yith_wcmv_commissions_user_actions', $rec );
                    break;

                case 'date':
                    $date   = $rec->get_date();
                    $t_time = date_i18n( __( 'Y/m/d g:i:s A', 'yith-woocommerce-product-vendors' ), mysql2date( 'U', $date ) );
                    $m_time = $date;
                    $time   = mysql2date( 'G', $date );

                    $time_diff = time() - $time;

                    if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
                        $h_time = sprintf( __( '%s ago', 'yith-woocommerce-product-vendors' ), human_time_diff( $time ) );
                    }
                    else {
                        $h_time = mysql2date( __( 'Y/m/d', 'yith-woocommerce-product-vendors' ), $m_time );
                    }

                    echo $h_time ? '<abbr title="' . $t_time . '">' . $h_time . '</abbr>' : '<small class="meta">-</small>';
                    break;

                case 'date_edit':
                    $date   = ! empty( $rec->last_edit ) && strpos( $rec->last_edit, '0000-00-00' ) === false ? $rec->last_edit : $rec->get_date();
                    $t_time = date_i18n( __( 'Y/m/d g:i:s A' ), mysql2date( 'U', $date ) );
                    $m_time = $date;
                    $time   = mysql2date( 'G', ! empty( $rec->last_edit_gmt ) && strpos( $rec->last_edit_gmt, '0000-00-00' ) ? $rec->last_edit : $rec->get_date() );

                    $time_diff = time() - $time;

                    if ( $time_diff > 0 && $time_diff < WEEK_IN_SECONDS )
                        $h_time = sprintf( __( '%s ago', 'yith-woocommerce-product-vendors' ), human_time_diff( $time ) );
                    else
                        $h_time = mysql2date( __( 'Y/m/d', 'yith-woocommerce-product-vendors' ), $m_time );

                    echo $h_time ? '<abbr title="' . $t_time . '">' . $h_time . '</abbr>' : '<small class="meta">-</small>';
                    break;

	            case 'to_pay':
		            /** @var WC_Order $order */
		            $order = wc_get_order( $rec->order_id );
		            $args = array();

		            if( $order instanceof WC_Order ){
			            $args['currency'] = yith_wcmv_get_order_currency( $order );
		            }
		            return $rec->get_amount_to_pay('display', $args );
		            break;

	            case 'refunded':
		            /** @var WC_Order $order */
		            $order = wc_get_order( $rec->order_id );
		            $args = array();

		            if( $order instanceof WC_Order ){
			            $args['currency'] = yith_wcmv_get_order_currency( $order );
		            }
		            return $rec->get_amount_refunded('display', $args );
		            break;

	            default:
	            	do_action( "yith_wcmv_commissions_list_table_col_{$column_name}", $rec, $this->_vendor, $column_name );
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
         * @since 1.0.0
         */
        public function column_cb( $rec ) {
            return sprintf(
                '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                $this->_args['plural'], //Let's simply repurpose the table's plural label
                $rec->id //The value of the checkbox should be the record's id
            );
        }

        /**
         * Message to be displayed when there are no items
         *
         * @since 3.1.0
         * @access public
         */
        public function no_items() {
            _e( 'No commissions found.', 'yith-woocommerce-product-vendors' );
        }


        /**
         * Extra controls to be displayed between bulk actions and pagination
         *
         * @since  1.0.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         *
         * @return string The view name
         */
        public function get_current_view() {
            return 'all';
        }
    }
}

