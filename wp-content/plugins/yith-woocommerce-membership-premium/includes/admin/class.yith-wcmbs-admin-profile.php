<?php
/**
 * Add extra profile fields for users in admin.
 *
 *
 * @author  Yithemes
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */

if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( !class_exists( 'YITH_WCMBS_Admin_Profile' ) ) {

    /**
     * YITH_WCMBS_Admin_Profile Class
     */
    class YITH_WCMBS_Admin_Profile {

        /**
         * Single instance of the class
         *
         * @var \YITH_WCMBS_Admin_Profile
         * @since 1.0.0
         */
        protected static $_instance;

        /**
         * Returns single instance of the class
         *
         * @return \YITH_WCMBS_Admin_Profile
         * @since 1.0.0
         */
        public static function get_instance() {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            return !is_null( $self::$_instance ) ? $self::$_instance : $self::$_instance = new $self;
        }

        /**
         * Hook in tabs.
         */
        protected function __construct() {
            // add membership info in User List Columns
            add_filter( 'manage_users_columns', array( $this, 'add_membership_columns' ) );
            add_filter( 'manage_users_custom_column', array( $this, 'render_membership_columns' ), 10, 3 );
        }

        /**
         * Add column in admin table list
         *
         * @param array $columns
         *
         * @return array
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         */
        public function add_membership_columns( $columns ) {
            $columns[ 'yith_wcmbs_user_membership_plans' ] = __( 'Membership Plans', 'yith-woocommerce-membership' );

            return $columns;
        }

        /**
         * Add column in admin table list
         *
         * @param string $output
         * @param string $column_name
         * @param int    $user_id
         *
         * @access public
         * @since  1.0.0
         * @author Leanza Francesco <leanzafrancesco@gmail.com>
         * @return string
         */
        public function render_membership_columns( $output, $column_name, $user_id ) {
            if ( $column_name == 'yith_wcmbs_user_membership_plans' ) {
                $member = YITH_WCMBS_Members()->get_member( $user_id );

                $membership_plans_status           = apply_filters( 'yith_wcmbs_admin_profile_membership_columns_membership_plans_status', 'any', $output, $column_name, $user_id );
                $membership_plans_args             = array( 'status' => $membership_plans_status );
                $membership_plans_args             = apply_filters( 'yith_wcmbs_admin_profile_membership_columns_membership_plans_args', $membership_plans_args, $output, $column_name, $user_id );
                $membership_plans_args[ 'return' ] = 'complete';

                $member_plans = $member->get_membership_plans( $membership_plans_args );
                if ( !empty( $member_plans ) ) {

                    $ret = '';
                    foreach ( $member_plans as $membership ) {
                        if ( $membership instanceof YITH_WCMBS_Membership ) {
                            $ret .= apply_filters( 'yith_wcmbs_output_membership_column', $membership->get_plan_info_span(), $membership );
                        }
                    }

                    return $ret;
                }
            }

            return $output;
        }

    }

}

/**
 * Unique access to instance of YITH_WCMBS_Admin_Profile class
 *
 * @return YITH_WCMBS_Admin_Profile|YITH_WCMBS_Admin_Profile_Premium
 * @since 1.3.2
 */
function YITH_WCMBS_Admin_Profile() {
    return YITH_WCMBS_Admin_Profile::get_instance();
}
