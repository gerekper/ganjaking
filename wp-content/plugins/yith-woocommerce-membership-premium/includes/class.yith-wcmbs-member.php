<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Member Class
 *
 * @class   YITH_WCMBS_Member
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 *
 */
class YITH_WCMBS_Member {

    /**
     * User id of member
     *
     * @var int
     * @since 1.0.0
     */
    public $id;

    /**
     * User
     *
     * @var WP_User
     * @since 1.0.0
     */
    public $user;

    /**
     * Constructor
     *
     * @access public
     * @since  1.0.0
     */
    public function __construct( $user_id ) {
        $this->id   = $user_id;
        $this->user = get_user_by( 'id', $user_id );
    }

    /**
     * return true if user has membership plan
     *
     * @access public
     * @since  1.0.0
     * @return YITH_WCMBS_Membership[]|bool
     */
    public function is_member() {
        if ( !$this->is_valid() )
            return false;

        $user_plans = $this->get_plans();

        if ( !empty( $user_plans ) ) {
            return true;
        }

        return false;
    }

    /**
     * return true if user is a valid User
     *
     * @access public
     * @since  1.0.0
     * @return YITH_WCMBS_Membership[]|bool
     */
    public function is_valid(){
        return !!$this->id && !!$this->user;
    }

    /**
     * Get all membership plans for this member
     *
     * @access public
     * @since  1.0.0
     * @return YITH_WCMBS_Membership[]|bool
     */
    public function get_plans() {
        if ( !$this->is_valid() )
            return false;

        $user_plans = YITH_WCMBS_Membership_Helper()->get_memberships_by_user( $this->id );

        return $user_plans;
    }

    /**
     * Get all membership plans
     *
     * @param array $args
     *
     * @access public
     * @since  1.0.0
     * @return YITH_WCMBS_Membership[]|array
     */
    public function get_membership_plans( $args = array() ) {
        if ( !$this->is_valid() )
            return array();

        $default_args = array(
            'return'         => 'id',
            'status'         => array( 'active', 'resumed', 'expiring' ), // active statuses
            'sort_by_date'   => false,
            'include_linked' => false,
            'history'        => false,
        );

        $args = wp_parse_args( $args, $default_args );

        $status = (array) $args[ 'status' ];

        $user_plans = $this->get_plans();

        $member_plans = array();

        if ( !empty( $user_plans ) && is_array( $user_plans ) ) {
            foreach ( $user_plans as $plan ) {
                if ( $plan instanceof YITH_WCMBS_Membership ) {
                    if ( in_array( $plan->status, $status ) || $args[ 'status' ] == 'any' ) {
                        if ( $args[ 'return' ] == 'id' ) {
                            $member_plans[] = $plan->plan_id;
                        } elseif ( $args[ 'return' ] == 'id_date' ) {
                            $member_plans[] = array(
                                'id'   => $plan->plan_id,
                                'date' => $plan->start_date,
                            );
                        } elseif ( $args[ 'return' ] == 'array_complete' ) {
                            $member_plans[ $plan->plan_id ][] = $plan;
                        } elseif ( $args[ 'return' ] == 'complete' ) {
                            $member_plans[] = $plan;
                        } elseif ( $args[ 'return' ] == 'names' ) {
                            $plan_name      = get_the_title( $plan->plan_id );
                            $member_plans[] = $plan_name;
                        }
                    }
                }
            }
        }

        return $member_plans;
    }

    /**
     * create a membership for this user
     *
     * @param int $plan_id  the id of the plan
     * @param int $order_id the id of the order. 0 if the membership is created by admin
     *
     * @access public
     * @since  1.0.0
     * @return bool
     */
    public function create_membership( $plan_id, $order_id = 0 ) {
        $membership_meta_data = array(
            'plan_id'    => 0,
            'title'      => get_option( 'yith-wcmbs-membership-name', _x( 'Membership', 'Default value for Membership Plan Name', 'yith-woocommerce-membership' ) ),
            'start_date' => time(),
            'end_date'   => 'unlimited',
            'order_id'   => $order_id,
            'user_id'    => $this->id,
            'status'     => 'active',
        );
        /* create the Membership */
        $membership = new YITH_WCMBS_Membership( 0, $membership_meta_data );
    }
}