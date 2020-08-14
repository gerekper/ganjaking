<?php
if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( !class_exists( 'YITH_WCMBS_Download_Reports_Ajax_Table' ) ) {
    /**
     * List table class
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCMBS_Download_Reports_Ajax_Table extends WP_List_Table {

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         *
         * @param array $args
         */
        public function __construct( $args = array() ) {
            $args = wp_parse_args( $args, array(
                'singular' => 'yith_wcmbs_download_report',
                'plural'   => 'yith_wcmbs_download_reports',
                'ajax'     => true,
                'screen'   => 'yith-wcmbs-download-reports-list',
            ) );
            parent::__construct( $args );
        }

        public function get_columns() {
            die( 'function YITH_WCMBS_Download_Reports_Ajax_Table::get_columns() must be over-ridden in a sub-class.' );
        }

        protected function get_sortable_columns() {
            return array();
        }

        public function display() {
            wp_nonce_field( 'yith-wcmbs-ajax-table-nonce', '_yith_wcmbs_ajax_table_nonce' );

            if ( is_array( $this->_pagination_args ) ) {
                foreach ( $this->_pagination_args as $key => $value ) {
                    if ( in_array( $key, array( 'total_items', 'per_page', 'total_pages' ) ) )
                        continue;

                    echo "<input id='$key' type='hidden' name='$key' value='$value' />";
                }
            }

            parent::display();
        }

        /**
         * Generates content for a single row of the table
         * by adding the possibility to add a class in the row
         *
         * @access public
         *
         * @param object $item The current item
         */
        public function single_row( $item ) {
            $row_classes = apply_filters( 'yith_wcmbs_download_reports_ajax_table_tr_class', array(), $item, $this );
            $row_classes = !!$row_classes && is_array( $row_classes ) ? implode( ' ', $row_classes ) : '';

            echo "<tr class='$row_classes'>";
            $this->single_row_columns( $item );
            echo '</tr>';
        }

        public function ajax_response() {

            check_ajax_referer( 'yith-wcmbs-ajax-table-nonce', '_yith_wcmbs_ajax_table_nonce' );

            $this->prepare_items();

            extract( $this->_args );
            extract( $this->_pagination_args, EXTR_SKIP );

            ob_start();
            if ( !empty( $_REQUEST[ 'no_placeholder' ] ) )
                $this->display_rows(); else
                $this->display_rows_or_placeholder();
            $rows = ob_get_clean();

            ob_start();
            $this->print_column_headers();
            $headers = ob_get_clean();

            ob_start();
            $this->pagination( 'top' );
            $pagination_top = ob_get_clean();

            ob_start();
            $this->pagination( 'bottom' );
            $pagination_bottom = ob_get_clean();

            $response                             = array( 'rows' => $rows );
            $response[ 'pagination' ][ 'top' ]    = $pagination_top;
            $response[ 'pagination' ][ 'bottom' ] = $pagination_bottom;
            $response[ 'column_headers' ]         = $headers;

            if ( isset( $total_items ) )
                $response[ 'total_items_i18n' ] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

            if ( isset( $total_pages ) ) {
                $response[ 'total_pages' ]      = $total_pages;
                $response[ 'total_pages_i18n' ] = number_format_i18n( $total_pages );
            }

            die( json_encode( $response ) );
        }
    }
}