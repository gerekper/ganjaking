<?php
if ( !defined( 'YITH_WCMBS' ) ) {
    exit;
} // Exit if accessed directly

if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if ( !class_exists( 'YITH_WCMBS_Members_List_Table' ) ) {
    /**
     * List table class
     *
     * @since    1.0.0
     * @author   Leanza Francesco <leanzafrancesco@gmail.com>
     */
    class YITH_WCMBS_Members_List_Table extends WP_List_Table {

        public $columns;
        public $hidden;
        public $sortable;

        /**
         * Constructor
         *
         * @access public
         * @since  1.0.0
         */
        public function __construct( $columns = array(), $hidden = array(), $sortable = array() ) {
            global $status, $page;

            $this->columns  = $columns;
            $this->hidden   = $hidden;
            $this->sortable = $sortable;

            //Set parent defaults
            parent::__construct( array(
                'singular' => 'yith_wcmbs_member',
                'plural'   => 'yith_wcmbs_members',
                'ajax'     => true,
                'screen'   => 'yith-wcmbs-members-list'
            ) );
        }

        public function get_columns() {
            $columns = array(
                'member'                           => __( 'Member', 'yith-woocommerce-membership' ),
                'yith_wcmbs_user_membership_plans' => __( 'Membership Plans', 'yith-woocommerce-membership' ),
            );

            return $columns;
        }

        public function get_sortable() {
            $sortable = array(
                'member' => array( 'member', false ),
            );

            return $sortable;
        }

        public function get_hidden() {
            return array();
        }

        public function prepare_items( $items = array() ) {
            $per_page = !empty( $_REQUEST[ 'f_per_page' ] ) && intval( $_REQUEST[ 'f_per_page' ] ) > 0 ? intval( $_REQUEST[ 'f_per_page' ] ) : 10;

            $columns  = $this->get_columns();
            $hidden   = $this->get_hidden();
            $sortable = $this->get_sortable();

            $this->_column_headers = array( $columns, $hidden, $sortable );

            $my_items = !empty( $items ) ? $items : $this->get_members();
            usort( $my_items, array( $this, 'usort_reorder' ) );

            $current_page = $this->get_pagenum();
            $total_items  = count( $my_items );

            $my_items = array_slice( $my_items, ( ( $current_page - 1 ) * $per_page ), $per_page );

            $this->items = $my_items;

            $this->set_pagination_args( array(
                //WE have to calculate the total number of items
                'total_items' => $total_items,
                //WE have to determine how many items to show on a page
                'per_page'    => $per_page,
                //WE have to calculate the total number of pages
                'total_pages' => ceil( $total_items / $per_page ),
                // Set ordering values if needed (useful for AJAX)
                'orderby'     => !empty( $_REQUEST[ 'orderby' ] ) && '' != $_REQUEST[ 'orderby' ] ? $_REQUEST[ 'orderby' ] : 'user',
                'order'       => !empty( $_REQUEST[ 'order' ] ) && '' != $_REQUEST[ 'order' ] ? $_REQUEST[ 'order' ] : 'asc'
            ) );
        }

        /**
         * @return array
         */
        public function get_members() {
            $items = array();

            $vendor = yith_get_vendor( 'current', 'user' );

            if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
                $users = get_users( array(
                    'fields' => 'ids'
                ) );


                $vendor_members = array();
                if ( !empty( $users ) ) {
                    foreach ( $users as $user_id ) {
                        $member       = YITH_WCMBS_Members()->get_member( $user_id );
                        $member_plans = $member->get_membership_plans( array( 'return' => 'id', 'status' => 'any' ) );

                        if ( $member_plans ) {
                            foreach ( $member_plans as $plan_id ) {
                                $vendor_id = wp_get_post_terms( $plan_id, YITH_Vendors()->get_taxonomy_name(), array( "fields" => "ids" ) );

                                if ( $vendor_id && in_array( $vendor->id, $vendor_id ) ) {
                                    $vendor_members[] = $user_id;
                                    break;
                                }
                            }
                        }
                    }
                }
                $vendor_members = array_unique( $vendor_members );

                if ( !empty( $vendor_members ) ) {
                    foreach ( $vendor_members as $member_id ) {
                        $item = array();
                        $user = get_user_by( 'id', $member_id );
                        if ( $user ) {
                            $item[ 'ID' ]     = $member_id;
                            $item[ 'member' ] = $user->user_login;
                            $member           = YITH_WCMBS_Members()->get_member( $member_id );
                            $member_plans     = $member->get_membership_plans( array( 'return' => 'complete', 'status' => 'any' ) );

                            if ( $member_plans ) {
                                foreach ( $member_plans as $membership ) {
                                    if ( $membership instanceof YITH_WCMBS_Membership ) {
                                        $vendor_id = wp_get_post_terms( $membership->plan_id, YITH_Vendors()->get_taxonomy_name(), array( "fields" => "ids" ) );

                                        if ( $vendor_id && in_array( $vendor->id, $vendor_id ) ) {
                                            $item[ 'yith_wcmbs_user_membership_plans' ][] = $membership;
                                        }
                                    }
                                }
                            }
                            $items[] = $item;
                        }
                    }
                }
            }

            return $items;
        }

        function column_default( $item, $column_name ) {
            switch ( $column_name ) {

                case 'yith_wcmbs_user_membership_plans':
                    $memberships = $item[ $column_name ];
                    if ( !empty( $memberships ) ) {
                        $ret = '';
                        foreach ( $memberships as $membership ) {
                            if ( $membership instanceof YITH_WCMBS_Membership ) {
                                $p_name  = $membership->get_plan_title();
                                $p_dates = $membership->get_plan_info_html();
                                $ret .= "<span class='yith-wcmbs-users-membership-info tips {$membership->status}' data-tip='{$p_dates}'>{$p_name}</span>  ";
                            }
                        }

                        return $ret;
                    }

                    break;

                default:
                    return $item[ $column_name ];
                    break;
            }
        }


        function usort_reorder( $a, $b ) {
            // If no sort, default to title
            $orderby = ( !empty( $_GET[ 'orderby' ] ) ) ? $_GET[ 'orderby' ] : 'member';
            // If no order, default to asc
            $order = ( !empty( $_GET[ 'order' ] ) ) ? $_GET[ 'order' ] : 'asc';

            // Determine sort order
            switch ( $orderby ) {
                default:
                    $result = strcmp( $a[ $orderby ], $b[ $orderby ] );

                    // Send final sort direction to usort
                    return ( $order === 'asc' ) ? $result : -$result;
            }
        }
    }
}
?>