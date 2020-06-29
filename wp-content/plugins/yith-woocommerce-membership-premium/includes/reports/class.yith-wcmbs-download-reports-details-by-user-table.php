<?php
if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCMBS_Download_Reports_Details_By_User_Table' ) ) {
    /**
     * List table class
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCMBS_Download_Reports_Details_By_User_Table extends YITH_WCMBS_Download_Reports_Ajax_Table {

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct() {
            parent::__construct( array(
                                     'singular' => 'yith_wcmbs_download_reports_detail_by_user',
                                     'plural'   => 'yith_wcmbs_download_reports_details_by_user',
                                     'ajax'     => true,
                                     'screen'   => 'yith-wcmbs-download-reports-details-by-user-list',
                                 ) );
        }

        public function get_columns() {
            $columns = array(
                'product_id' => __( 'Product ID', 'yith-woocommerce-membership' ),
                'product'    => __( 'Product', 'yith-woocommerce-membership' ),
                'ip_address' => __( 'IP Address', 'yith-woocommerce-membership' ),
                'date'       => __( 'Date', 'yith-woocommerce-membership' ),
            );

            return apply_filters( 'yith_wcmbs_download_reports_details_by_user_manage_columns', $columns );
        }

        protected function get_sortable_columns() {
            return array(
                'product_id' => array( 'product_id', false ),
                'date'       => array( 'date', false ),
            );
        }

        public function prepare_items() {
            $user_id = isset( $_REQUEST[ 'user_id' ] ) ? absint( $_REQUEST[ 'user_id' ] ) : false;
            if ( !$user_id )
                return;

            $current_page = $this->get_pagenum();
            $per_page     = !empty( $_REQUEST[ 'per_page' ] ) && intval( $_REQUEST[ 'per_page' ] ) > 0 ? intval( $_REQUEST[ 'per_page' ] ) : 20;

            $order_by = !empty( $_REQUEST[ 'orderby' ] ) ? $_REQUEST[ 'orderby' ] : 'date';
            $order    = !empty( $_REQUEST[ 'order' ] ) ? $_REQUEST[ 'order' ] : 'DESC';

            $query_args = array(
                'select'   => 'product_id, type, user_ip_address, timestamp_date as date',
                'where'    => array(
                    array(
                        'key'   => 'user_id',
                        'value' => $user_id,
                    )
                ),
                'order_by' => $order_by,
                'order'    => $order
            );

            $results = YITH_WCMBS_Downloads_Report()->get_download_reports( $query_args );

            $total_items = count( $results );
            $total_pages = absint( ceil( $total_items / $per_page ) );

            $this->items = array_splice( $results, ( $current_page - 1 ) * $per_page, $per_page );


            $this->set_pagination_args( array(
                                            'total_items' => $total_items,
                                            'per_page'    => $per_page,
                                            'total_pages' => $total_pages,
                                            // Set ordering values if needed (useful for AJAX)
                                            'orderby'     => $order_by,
                                            'order'       => $order,
                                        ) );
        }

        function column_default( $item, $column_name ) {
            $r = '';

            switch ( $column_name ) {
                case 'product_id':
                    $r = $item->product_id;
                    break;
                case 'product':
                    $product_id = $item->product_id;
                    $product    = wc_get_product( $product_id );

                    if ( !!$product ) {
                        $edit_link = get_edit_post_link( $product_id );
                        $r         .= ' <a target="_blank" href="' . $edit_link . '">';
                        $r         .= $product->get_title();
                        $r         .= '</a>';
                    }

                    break;
                case 'date':
                    $date      = mysql2date( 'Y/m/d', $item->date );
                    $full_date = mysql2date( wc_date_format() . ' ' . wc_time_format(), $item->date );
                    $r         = '<abbr title="' . $full_date . '">' . $date . '</abbr>';

                    break;
                case 'ip_address':
                    $r = $item->user_ip_address;

                    break;
                default:
                    break;
            }

            echo apply_filters( 'yith_wcmbs_download_reports_details_by_user_column_default', $r, $item, $column_name );

            do_action( 'yith_wcmbs_download_reports_details_by_user_manage_custom_column', $item, $column_name );
        }
    }
}