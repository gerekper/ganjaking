<?php
if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'YITH_WCMBS_Download_Reports_By_User_Table' ) ) {
    /**
     * List table class
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCMBS_Download_Reports_By_User_Table extends YITH_WCMBS_Download_Reports_Ajax_Table {

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct() {
            parent::__construct( array(
                                     'singular' => 'yith_wcmbs_download_report_by_user',
                                     'plural'   => 'yith_wcmbs_download_reports_by_user',
                                     'ajax'     => true,
                                     'screen'   => 'yith-wcmbs-download-reports-by-user-list',
                                 ) );
        }

        public function get_columns() {
            $columns = array(
                'user'                => __( 'User', 'yith-woocommerce-membership' ),
                'downloads'           => __( 'Downloads', 'yith-woocommerce-membership' ),
                'different-downloads' => __( 'Different Downloads', 'yith-woocommerce-membership' ),
            );

            $show_membership_info = get_option( 'yith-wcmbs-show-membership-info-in-reports', 'yes' ) === 'yes';
            if ( $show_membership_info ) {
                $columns[ 'membership_info' ] = __( 'Membership Info', 'yith-woocommerce-membership' );
            }

            $custom_columns = apply_filters( 'yith_wcmbs_download_reports_by_user_custom_columns', array() );
            $end_columns    = array( 'report_actions' => '' );

            $cols = array_merge( $columns, $custom_columns, $end_columns );

            return apply_filters( 'yith_wcmbs_download_reports_by_user_manage_columns', $cols );
        }

        protected function get_sortable_columns() {
            return array(
                'user'                => array( 'user', false ),
                'downloads'           => array( 'downloads', false ),
                'different-downloads' => array( 'distinct_downloads', false ),
            );
        }

        public function prepare_items() {
            $current_page = $this->get_pagenum();
            $per_page     = !empty( $_REQUEST[ 'per_page' ] ) && intval( $_REQUEST[ 'per_page' ] ) > 0 ? intval( $_REQUEST[ 'per_page' ] ) : 20;

            $order_by = !empty( $_REQUEST[ 'orderby' ] ) ? $_REQUEST[ 'orderby' ] : 'downloads';
            $order    = !empty( $_REQUEST[ 'order' ] ) ? $_REQUEST[ 'order' ] : 'DESC';
            if ( $order_by === 'user' )
                $order_by = 'user_id';


            $query_args = array(
                'group_by' => 'user_id',
                'select'   => 'user_id, COUNT(product_id) as downloads, COUNT(Distinct product_id) as distinct_downloads',
                'order_by' => $order_by,
                'order'    => $order
            );

            $results     = YITH_WCMBS_Downloads_Report()->get_download_reports( $query_args );
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
                case 'user':
                    $user_id   = $item->user_id;
                    $user_info = get_userdata( $user_id );

                    $r = sprintf( _x( '#%s', 'Download reports table: user id', 'yith-woocommerce-membership' ), $user_id );

                    if ( !empty( $user_info ) ) {
                        $r .= ' <a target="_blank" href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';
                        $r .= esc_html( $user_info->display_name );
                        $r .= '</a>';
                    }
                    break;
                case 'downloads':
                    $r = $item->downloads;
                    break;
                case 'different-downloads':
                    $r = $item->distinct_downloads;
                    break;

                case 'report_actions':
                    $actions = array();

                    $user_id   = $item->user_id;
                    $user_info = get_userdata( $user_id );

                    $user_name = sprintf( _x( 'User #%s', 'Download reports table title: user id', 'yith-woocommerce-membership' ), $user_id );

                    if ( !empty( $user_info ) ) {
                        $user_name = esc_html( $user_info->display_name );
                    }

                    $actions[ 'details' ] = array(
                        'url'    => '#',
                        'name'   => __( 'Details', 'yith-woocommerce-membership' ),
                        'action' => 'details',
                        'data'   => array(
                            'user_id'   => $item->user_id,
                            'user_name' => $user_name,
                        )
                    );

                    foreach ( $actions as $action ) {
                        $current_action = esc_attr( $action[ 'action' ] );
                        $current_url    = esc_url( $action[ 'url' ] );
                        $current_name   = esc_attr( $action[ 'name' ] );
                        $current_data   = '';
                        if ( isset( $action[ 'data' ] ) ) {
                            foreach ( $action[ 'data' ] as $key => $value ) {
                                $current_data .= "data-$key='$value' ";
                            }
                        }

                        $r .= "<a class='button tips $current_action' href='$current_url' $current_data data-tip='$current_name'>$current_name</a>";
                    }
                    break;

                case 'membership_info':
                    $user_id      = $item->user_id;
                    $member       = YITH_WCMBS_Members()->get_member( $user_id );
                    $member_plans = $member->get_membership_plans( array( 'return' => 'complete', 'status' => 'any' ) );
                    if ( !empty( $member_plans ) ) {

                        $membership_info = '';
                        foreach ( $member_plans as $membership ) {
                            if ( $membership instanceof YITH_WCMBS_Membership ) {
                                $membership_info .= $membership->get_plan_info_span();
                            }
                        }

                        echo $membership_info;
                    }
                    break;

                default:
                    break;
            }

            echo apply_filters( 'yith_wcmbs_download_reports_by_user_column_default', $r, $item, $column_name );

            do_action( 'yith_wcmbs_download_reports_by_user_manage_custom_column', $item, $column_name );
        }

    }
}