<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

/**
 * Dynamic Pricing Compatibility Class
 *
 * @class   YITH_WCMBS_Dynamic_Pricing_Compatibility
 * @package Yithemes
 * @since   1.0.0
 * @author  Yithemes
 */
class YITH_WCMBS_Dynamic_Pricing_Compatibility {

    /**
     * Single instance of the class
     *
     * @var \YITH_WCMBS_Dynamic_Pricing_Compatibility
     * @since 1.0.0
     */
    protected static $_instance;

    /**
     * Returns single instance of the class
     *
     * @return \YITH_WCMBS_Dynamic_Pricing_Compatibility
     */
    public static function get_instance() {
        return !is_null( self::$_instance ) ? self::$_instance : self::$_instance = new self();
    }

    /**
     * Constructor
     *
     * @access public
     */
    protected function __construct() {
        add_filter( 'yit_ywdpd_sub_rules_valid', array( $this, 'validate_user_rule' ), 10, 3 );
        add_filter( 'yit_ywdpd_validate_user', array( $this, 'validate_user' ), 10, 3 );

        add_filter( 'ywdpd_pricing_discount_metabox', array( $this, 'add_membership_fields_in_pricing_rules_metabox' ) );

        /**
         * integration with YITH WooCommerce Dynamic Pricing and Discounts 1.5.3
         * membership options in Cart Rules
         *
         * @since 1.3.16
         */
        add_filter( 'yit_ywdpd_cart_rules_options', array( $this, 'add_membership_fields_in_cart_rules' ) );
        add_action( 'yith_ywdpd_cart_rules_discount_value_row', array( $this, 'print_membership_select_in_cart_rule_values' ), 10, 4 );

    }

    /**
     * add Membership fields in Cart Rules
     *
     * @param $options
     * @since 1.3.16
     * @return array
     */
    public function add_membership_fields_in_cart_rules( $options ) {
        if ( $options && isset( $options[ 'rules_type' ], $options[ 'rules_type' ][ 'customers' ], $options[ 'rules_type' ][ 'customers' ][ 'options' ] ) ) {
            $options[ 'rules_type' ][ 'customers' ][ 'options' ][ 'memberships_list' ]          = __( 'Include customer with membership plans', 'yith-woocommerce-membership' );
            $options[ 'rules_type' ][ 'customers' ][ 'options' ][ 'excluded_memberships_list' ] = __( 'Exclude customer with membership plans', 'yith-woocommerce-membership' );
        }
        return $options;
    }

    /**
     * add Membership select in Cart Rules Values Row
     *
     * @param $db_value
     * @param $i
     * @param $name
     * @param $id
     * @since 1.3.16
     */
    public function print_membership_select_in_cart_rule_values( $db_value, $i, $name, $id ) {
        $plan_posts = YITH_WCMBS_Manager()->get_plans();
        $plans      = array();
        if ( !!$plan_posts && is_array( $plan_posts ) ) {
            foreach ( $plan_posts as $plan_post ) {
                $plans[ $plan_post->ID ] = $plan_post->post_title;
            }
        }

        $selects = array( 'memberships_list' => 'rules_type_memberships_list', 'excluded_memberships_list' => 'rules_type_excluded_memberships_list' );

        foreach ( $selects as $type => $key ) {
            echo "<tr class='deps-rules_type' data-type='{$type}'>";
            echo "<td>";
            $value = isset( $db_value[ $i ][ $key ] ) ? $db_value[ $i ][ $key ] : array();

            yith_plugin_fw_get_field( array(
                                          'type'              => 'select',
                                          'class'             => 'wc-enhanced-select',
                                          'name'              => $name . "[{$i}][{$key}]",
                                          'id'                => $id . "[{$i}][{$key}]",
                                          'multiple'          => true,
                                          'desc'              => '',
                                          'data'              => array(
                                              'placeholder' => __( 'Select plans', 'yith-woocommerce-membership' ),
                                          ),
                                          'custom_attributes' => 'style="width:100%"',
                                          'options'           => $plans,
                                          'value'             => $value
                                      ), true, false );
            echo "</td>";
            echo "</tr>";
        }
    }

    /**
     * add Membership fields in Pricing Rules Metabox
     *
     * @since 1.3.5
     */
    public function add_membership_fields_in_pricing_rules_metabox( $options ) {
        $options[ 'user_rules' ][ 'options' ][ 'memberships_list' ]          = __( 'Include customer with membership plans', 'yith-woocommerce-membership' );
        $options[ 'user_rules' ][ 'options' ][ 'excluded_memberships_list' ] = __( 'Exclude customer with membership plans', 'yith-woocommerce-membership' );

        $plan_posts = YITH_WCMBS_Manager()->get_plans();
        $plans      = array();
        if ( !!$plan_posts && is_array( $plan_posts ) ) {
            foreach ( $plan_posts as $plan_post ) {
                $plans[ $plan_post->ID ] = $plan_post->post_title;
            }
        }

        $to_insert = array(
            'user_rules_memberships_list'          => array(
                'label'    => __( 'Select plans to include', 'yith-woocommerce-membership' ),
                'type'     => 'select',
                'class'    => 'wc-enhanced-select',
                'multiple' => true,
                'desc'     => '',
                'data'     => array(
                    'placeholder' => __( 'Select plans', 'yith-woocommerce-membership' ),
                ),
                'options'  => $plans,
                'deps'     => array(
                    'ids'    => '_user_rules',
                    'values' => 'memberships_list'
                )
            ),
            'user_rules_excluded_memberships_list' => array(
                'label'    => __( 'Select plans to exclude', 'yith-woocommerce-membership' ),
                'type'     => 'select',
                'class'    => 'wc-enhanced-select',
                'multiple' => true,
                'desc'     => '',
                'data'     => array(
                    'placeholder' => __( 'Select plans', 'yith-woocommerce-membership' ),
                ),
                'options'  => $plans,
                'deps'     => array(
                    'ids'    => '_user_rules',
                    'values' => 'excluded_memberships_list'
                )
            )
        );

        $position = array_search( 'user_rules', array_keys( $options ) ) + 1;
        $options  = array_merge( array_slice( $options, 0, $position, true ), $to_insert,
                                 array_slice( $options, $position, count( $options ) - $position ) );

        return $options;
    }


    /**
     * Add Membership in pricing rules options
     *
     * @param $options
     * @return mixed
     * @see called in init.php
     */
    public static function add_membership_in_pricing_rules_options( $options ) {
        if ( defined( 'YITH_YWDPD_PREMIUM' ) && YITH_YWDPD_PREMIUM && defined( 'YITH_YWDPD_VERSION' ) && version_compare( YITH_YWDPD_VERSION, '1.1.0', '>=' ) ) {
            $options[ 'user_rules' ][ 'memberships_list' ] = __( 'Include customer with membership plans', 'yith-woocommerce-membership' );
        }

        return $options;
    }


    /**
     * Validate user rule
     *
     * @param $sub_rules_valid
     * @param $discount_type
     * @param $r
     * @return bool
     */
    public function validate_user_rule( $sub_rules_valid, $discount_type, $r ) {
        if ( is_user_logged_in() && in_array( $discount_type, array( 'memberships_list', 'excluded_memberships_list' ) ) ) {

            if ( empty( $r[ 'rules_type_' . $discount_type ] ) ) {
                return $sub_rules_valid;
            }

            $member = YITH_WCMBS_Members()->get_member( get_current_user_id() );

            $member_has_one_plan_at_least = false;
            if ( is_array( $r[ 'rules_type_' . $discount_type ] ) ) {
                foreach ( $r[ 'rules_type_' . $discount_type ] as $plan_id ) {
                    if ( $member->has_active_plan( $plan_id, false ) ) {
                        $member_has_one_plan_at_least = true;
                        break;
                    }
                }

                if ( 'memberships_list' === $discount_type ) {
                    if ( !$member_has_one_plan_at_least ) {
                        $sub_rules_valid = false;
                    }
                } else {
                    if ( $member_has_one_plan_at_least ) {
                        $sub_rules_valid = false;
                    }
                }
            }
        }

        return $sub_rules_valid;
    }

    /**
     * Validate User
     *
     * @param $to_return
     * @param $type
     * @param $users_list
     * @return bool
     */
    public function validate_user( $to_return, $type, $users_list ) {
        if ( is_user_logged_in() && in_array( $type, array( 'memberships_list', 'excluded_memberships_list' ) ) ) {
            $member = YITH_WCMBS_Members()->get_member( get_current_user_id() );
            if ( is_array( $users_list ) ) {
                foreach ( $users_list as $plan_id ) {
                    if ( 'memberships_list' === $type && $member->has_active_plan( $plan_id, false ) ) {
                        return true;
                    } elseif ( 'excluded_memberships_list' === $type && !$member->has_active_plan( $plan_id, false ) ) {
                        return true;
                    }
                }
            }
        }

        return $to_return;
    }
}

/**
 * Unique access to instance of YITH_WCMBS_Dynamic_Pricing_Compatibility class
 *
 * @return YITH_WCMBS_Dynamic_Pricing_Compatibility
 * @since 1.0.0
 */
function YITH_WCMBS_Dynamic_Pricing_Compatibility() {
    return YITH_WCMBS_Dynamic_Pricing_Compatibility::get_instance();
}