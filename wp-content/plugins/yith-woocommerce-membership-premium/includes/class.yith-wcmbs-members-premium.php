<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Members Class
 *
 * @class   YITH_WCMBS_Members_Premium
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Members_Premium extends YITH_WCMBS_Members{
    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Members
     * @since 1.0.0
     */
    protected static $_instance;

    /**
     * Get a Member obj by user_id
     *
     * @param $user_id int the id of the user
     *
     * @access public
     * @return YITH_WCMBS_Member_Premium
     * @since  1.0.0
     */
    public function get_member( $user_id ) {
        $member = new YITH_WCMBS_Member_Premium( $user_id );

        return $member;
    }
}