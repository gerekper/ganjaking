<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSMemberFunction' ) ) {

    class RSMemberFunction {
        /* Redeeming Percentage */

        public static function redeem_points_percentage( $UserId ) {
            $PointValue = wc_format_decimal( get_option( 'rs_redeem_point_value' ) ) ;
            if ( empty( $UserId ) )
                return $PointValue ;

            $UserObj                      = new WP_User( $UserId ) ;
            $UserRole                     = $UserObj->roles ;
            $RoleofUser                   = $UserRole[ 0 ] ;
            $Pointsdata                   = new RS_Points_Data( $UserId ) ;
            $Points                       = get_option( 'rs_select_redeem_points_based_on' ) == '1' ? $Pointsdata->total_earned_points() : $Pointsdata->total_available_points() ;
            $RedeemLevelBasedonUserRole   = get_option( 'rs_enable_user_role_based_reward_points_for_redeem' ) ;
            $RedeemLevelBasedonPoints     = get_option( 'rs_enable_redeem_level_based_reward_points' ) ;
            $RedeemLevelBasedonHistory    = get_option( 'rs_enable_user_purchase_history_based_reward_points_redeem' ) ;
            $RedeemLevelBasedonMembership = class_exists( 'SUMOMemberships' ) ? get_option( 'rs_enable_membership_plan_based_reward_points' ) : 'no' ;
            /* UserRole Level Enabled */
            if ( ($RedeemLevelBasedonUserRole == 'yes') && ($RedeemLevelBasedonPoints != 'yes') && ($RedeemLevelBasedonMembership != 'yes') ) {
                $RolePercentage = get_option( 'rs_reward_user_role_for_redeem_' . $RoleofUser ) != '' ? get_option( 'rs_reward_user_role_for_redeem_' . $RoleofUser ) : 100 ;
                $Percentage     = ( $RedeemLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $RolePercentage , 'redeem' ) : $RolePercentage ;
                $percentvalue   = ($PointValue * ( float ) $Percentage) / 100 ;
                return $percentvalue ;
            }

            /* Earning Level Enabled */
            if ( ($RedeemLevelBasedonUserRole != 'yes') && ($RedeemLevelBasedonPoints == 'yes') && ($RedeemLevelBasedonMembership != 'yes') ) {
                $Rules            = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_for_redeem' ) , 'rewardpoints' ) ;
                $PointsPercentage = self::points_percentage( $Rules , $Points , 100 , 'redeem' , 'yes' ) ;
                $Percentage       = ( $RedeemLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $PointsPercentage , 'redeem' ) : $PointsPercentage ;
                $percentvalue     = ($PointValue * ( float ) $Percentage) / 100 ;
                return $percentvalue ;
            }

            /* Membership Level Enabled */
            if ( ($RedeemLevelBasedonUserRole != 'yes') && ($RedeemLevelBasedonPoints != 'yes') && ($RedeemLevelBasedonMembership == 'yes') ) {
                $MembershipPercentage = self::sumo_membership_percentage( $UserId , 100 , 'redeem' , 'yes' ) ;
                $Percentage           = ( $RedeemLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $MembershipPercentage , 'redeem' ) : $MembershipPercentage ;
                $percentvalue         = ($PointValue * ( float ) $Percentage) / 100 ;
                return $percentvalue ;
            }

            /* All Level Enabled */
            if ( ($RedeemLevelBasedonUserRole == 'yes') && ($RedeemLevelBasedonPoints == 'yes') && ($RedeemLevelBasedonMembership == 'yes') ) {
                $Rules                = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_for_redeem' ) , 'rewardpoints' ) ;
                $RolePercentage       = get_option( 'rs_reward_user_role_for_redeem_' . $RoleofUser ) != '' ? get_option( 'rs_reward_user_role_for_redeem_' . $RoleofUser ) : 100 ;
                $PointsPercentage     = self::points_percentage( $Rules , $Points , $RolePercentage , 'redeem' , 'no' ) ;
                $MembershipPercentage = self::sumo_membership_percentage( $UserId , $PointsPercentage , 'redeem' , 'no' ) ;
                $Percentage           = ( $RedeemLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $MembershipPercentage , 'redeem' ) : $MembershipPercentage ;
                $percentvalue         = ($PointValue * ( float ) $Percentage) / 100 ;
                return $percentvalue ;
            }

            /* UserRole Level Disabled */
            if ( ($RedeemLevelBasedonUserRole != 'yes') && ($RedeemLevelBasedonPoints == 'yes') && ($RedeemLevelBasedonMembership == 'yes') ) {
                $Rules                = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_for_redeem' ) , 'rewardpoints' ) ;
                $PointsPercentage     = self::points_percentage( $Rules , $Points , 100 , 'redeem' , 'no' ) ;
                $MembershipPercentage = self::sumo_membership_percentage( $UserId , $PointsPercentage , 'redeem' , 'no' ) ;
                $Percentage           = ( $RedeemLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $MembershipPercentage , 'redeem' ) : $MembershipPercentage ;
                $percentvalue         = ($PointValue * $Percentage) / 100 ;
                return $percentvalue ;
            }

            /* Earning Level Disabled */
            if ( ($RedeemLevelBasedonUserRole == 'yes') && ($RedeemLevelBasedonPoints != 'yes') && ($RedeemLevelBasedonMembership == 'yes') ) {
                $RolePercentage       = get_option( 'rs_reward_user_role_for_redeem_' . $RoleofUser ) != '' ? get_option( 'rs_reward_user_role_for_redeem_' . $RoleofUser ) : 100 ;
                $MembershipPercentage = self::sumo_membership_percentage( $UserId , $RolePercentage , 'redeem' , 'no' ) ;
                $Percentage           = ( $RedeemLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $MembershipPercentage , 'redeem' ) : $MembershipPercentage ;
                $percentvalue         = ($PointValue * ( float ) $Percentage) / 100 ;
                return $percentvalue ;
            }

            /* Membership Level Disabled */
            if ( ($RedeemLevelBasedonUserRole == 'yes') && ($RedeemLevelBasedonPoints == 'yes') && ($RedeemLevelBasedonMembership != 'yes') ) {
                $Rules            = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_for_redeem' ) , 'rewardpoints' ) ;
                $RolePercentage   = get_option( 'rs_reward_user_role_for_redeem_' . $RoleofUser ) != '' ? get_option( 'rs_reward_user_role_for_redeem_' . $RoleofUser ) : 100 ;
                $PointsPercentage = self::points_percentage( $Rules , $Points , $RolePercentage , 'redeem' , 'no' ) ;
                $Percentage       = ( $RedeemLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $PointsPercentage , 'redeem' ) : $PointsPercentage ;
                $percentvalue     = ($PointValue * ( float ) $Percentage) / 100 ;
                return $percentvalue ;
            }

            /* All Level Disabled */
            if ( ($RedeemLevelBasedonUserRole != 'yes') && ($RedeemLevelBasedonPoints != 'yes') && ($RedeemLevelBasedonMembership != 'yes') ) {
                $Percentage   = ( $RedeemLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , 100 , 'redeem' , 'yes' ) : 100 ;
                $percentvalue = ($PointValue * ( float ) $Percentage) / 100 ;
                return $percentvalue ;
            }
        }

        /* Earning Percentage */

        public static function earn_points_percentage( $UserId , $Points ) {
            if ( empty( $UserId ) ) {
                $Percentage = ($Points * 100) / 100 ;
                return $Percentage ;
            }

            $UserObj                    = new WP_User( $UserId ) ;
            $UserRole                   = $UserObj->roles ;
            $RoleofUser                 = $UserRole[ 0 ] ;
            $Pointsdata                 = new RS_Points_Data( $UserId ) ;
            $TotalPoints                = get_option( 'rs_select_earn_points_based_on' ) == '1' ? $Pointsdata->total_earned_points() : $Pointsdata->total_available_points() ;
            $EarnLevelBasedonUserRole   = get_option( 'rs_enable_user_role_based_reward_points' ) ;
            $EarnLevelBasedonPoints     = get_option( 'rs_enable_earned_level_based_reward_points' ) ;
            $EarnLevelBasedonMembership = class_exists( 'SUMOMemberships' ) ? get_option( 'rs_enable_membership_plan_based_reward_points' ) : 'no' ;
            $EarnLevelBasedonHistory    = get_option( 'rs_enable_user_purchase_history_based_reward_points' ) ;

            /* UserRole Level Enabled */
            if ( ($EarnLevelBasedonUserRole == 'yes') && ($EarnLevelBasedonPoints != 'yes') && ($EarnLevelBasedonMembership != 'yes') ) {
                $RolePercentage = get_option( 'rs_reward_user_role_' . $RoleofUser ) != '' ? get_option( 'rs_reward_user_role_' . $RoleofUser ) : 100 ;
                $Percentage     = ( $EarnLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $RolePercentage , 'earning' ) : $RolePercentage ;
                $Percentage     = ($Points * ( float ) $Percentage) / 100 ;
                return $Percentage ;
            }

            /* Earning Level Enabled */
            if ( ($EarnLevelBasedonUserRole != 'yes') && ($EarnLevelBasedonPoints == 'yes') && ($EarnLevelBasedonMembership != 'yes') ) {
                $Rules            = multi_dimensional_sort( get_option( 'rewards_dynamic_rule' ) , 'rewardpoints' ) ;
                $PointsPercentage = self::points_percentage( $Rules , $TotalPoints , 100 , 'earning' , 'yes' ) ;
                $Percentage       = ( $EarnLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $PointsPercentage , 'earning' ) : $PointsPercentage ;
                $Percentage       = ($Points * ( float ) $Percentage) / 100 ;
                return $Percentage ;
            }

            /* Membership Level Enabled */
            if ( ($EarnLevelBasedonUserRole != 'yes') && ($EarnLevelBasedonPoints != 'yes') && ($EarnLevelBasedonMembership == 'yes') ) {
                $MembershipPercentage = self::sumo_membership_percentage( $UserId , 100 , 'earning' , 'yes' ) ;
                $Percentage           = ( $EarnLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $MembershipPercentage , 'earning' ) : $MembershipPercentage ;
                $Percentage           = ($Points * ( float ) $Percentage) / 100 ;
                return $Percentage ;
            }

            /* All Level Enabled */
            if ( ($EarnLevelBasedonUserRole == 'yes') && ($EarnLevelBasedonPoints == 'yes') && ($EarnLevelBasedonMembership == 'yes') ) {
                $Rules                = multi_dimensional_sort( get_option( 'rewards_dynamic_rule' ) , 'rewardpoints' ) ;
                $RolePercentage       = get_option( 'rs_reward_user_role_' . $RoleofUser ) != '' ? get_option( 'rs_reward_user_role_' . $RoleofUser ) : 100 ;
                $PointsPercentage     = self::points_percentage( $Rules , $TotalPoints , $RolePercentage , 'earning' , 'no' ) ;
                $MembershipPercentage = self::sumo_membership_percentage( $UserId , $PointsPercentage , 'earning' , 'no' ) ;
                $Percentage           = ( $EarnLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $MembershipPercentage , 'earning' ) : $MembershipPercentage ;
                $Percentage           = ($Points * ( float ) $Percentage) / 100 ;
                return $Percentage ;
            }

            /* UserRole Level Disabled */
            if ( ($EarnLevelBasedonUserRole != 'yes') && ($EarnLevelBasedonPoints == 'yes') && ($EarnLevelBasedonMembership == 'yes') ) {
                $Rules                = multi_dimensional_sort( get_option( 'rewards_dynamic_rule' ) , 'rewardpoints' ) ;
                $PointsPercentage     = self::points_percentage( $Rules , $TotalPoints , 100 , 'earning' , 'no' ) ;
                $MembershipPercentage = self::sumo_membership_percentage( $UserId , $PointsPercentage , 'earning' , 'no' ) ;
                $Percentage           = ( $EarnLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $MembershipPercentage , 'earning' ) : $MembershipPercentage ;
                $Percentage           = ($Points * ( float ) $Percentage) / 100 ;
                return $Percentage ;
            }

            /* Earning Level Disabled */
            if ( ($EarnLevelBasedonUserRole == 'yes') && ($EarnLevelBasedonPoints != 'yes') && ($EarnLevelBasedonMembership == 'yes') ) {
                $RolePercentage       = get_option( 'rs_reward_user_role_' . $RoleofUser ) != '' ? get_option( 'rs_reward_user_role_' . $RoleofUser ) : 100 ;
                $MembershipPercentage = self::sumo_membership_percentage( $UserId , $RolePercentage , 'earning' , 'no' ) ;
                $Percentage           = ( $EarnLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $MembershipPercentage , 'earning' ) : $MembershipPercentage ;
                $Percentage           = ($Points * ( float ) $Percentage) / 100 ;
                return $Percentage ;
            }

            /* Membership Level Disabled */
            if ( ($EarnLevelBasedonUserRole == 'yes') && ($EarnLevelBasedonPoints == 'yes') && ($EarnLevelBasedonMembership != 'yes') ) {
                $Rules            = multi_dimensional_sort( get_option( 'rewards_dynamic_rule' ) , 'rewardpoints' ) ;
                $RolePercentage   = get_option( 'rs_reward_user_role_' . $RoleofUser ) != '' ? get_option( 'rs_reward_user_role_' . $RoleofUser ) : 100 ;
                $PointsPercentage = self::points_percentage( $Rules , $TotalPoints , $RolePercentage , 'earning' , 'no' ) ;
                $Percentage       = ( $EarnLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , $PointsPercentage , 'earning' ) : $PointsPercentage ;
                $Percentage       = ($Points * ( float ) $Percentage) / 100 ;
                return $Percentage ;
            }


            /* All Level Disabled */
            if ( ($EarnLevelBasedonUserRole != 'yes') && ($EarnLevelBasedonPoints != 'yes') && ($EarnLevelBasedonMembership != 'yes') ) {
                $Percentage = ( $EarnLevelBasedonHistory == 'yes' ) ? self::purchase_history_percentage( $UserId , 100 , 'earning' , 'yes' ) : 100 ;
                $Percentage = ($Points * ( float ) $Percentage) / 100 ;
                return $Percentage ;
            }
        }

        /* Percentage based on SUMO Membership */

        public static function sumo_membership_percentage( $UserId , $Percentage , $Type , $BoolVal ) {
            $MembershipPercentage = array() ;
            $args                 = array(
                'post_type'  => 'sumomembers' ,
                'meta_query' => array(
                    array(
                        'key'     => 'sumomemberships_userid' ,
                        'value'   => array( $UserId ) ,
                        'compare' => 'IN'
                    )
                ) ) ;
            $Posts                = get_posts( $args ) ;
            if ( ! isset( $Posts[ 0 ]->ID ) )
                return $Percentage ;

            $PostId = $Posts[ 0 ]->ID ;
            $Plans  = get_post_meta( $PostId , 'sumomemberships_saved_plans' , true ) ;
            if ( ! srp_check_is_array( $Plans ) )
                return $Percentage ;

            foreach ( $Plans as $Plan ) {
                if ( ! isset( $Plan[ 'choose_plan' ] ) && empty( $Plan[ 'choose_plan' ] ) )
                    $MembershipPercentage[] = 100 ;

                $PlanId                 = $Plan[ 'choose_plan' ] ;
                $PlanPercentage         = ($Type == 'earning') ? get_option( 'rs_reward_membership_plan_' . $PlanId ) : get_option( 'rs_reward_membership_plan_for_redeem' . $PlanId ) ;
                $MembershipPercentage[] = ! empty( $PlanPercentage ) ? $PlanPercentage : 100 ;
            }
            if ( ! srp_check_is_array( $MembershipPercentage ) )
                return $Percentage ;

            $Priority = ($Type == 'earning') ? get_option( 'rs_choose_priority_level_selection' ) : get_option( 'rs_choose_priority_level_selection_for_redeem' ) ;
            if ( $Priority == '1' ) {
                $Percentage = ($BoolVal == 'no') ? (($Percentage >= max( $MembershipPercentage )) ? $Percentage : max( $MembershipPercentage )) : max( $MembershipPercentage ) ;
            } else {
                $Percentage = ($BoolVal == 'no') ? (($Percentage <= min( $MembershipPercentage )) ? $Percentage : min( $MembershipPercentage )) : min( $MembershipPercentage ) ;
            }
            return $Percentage ;
        }

        /* Percentage based on Purcahsed History */

        public static function purchase_history_percentage( $UserId , $Percentage , $Type , $BoolVal = 'no' ) {
            $Rules = ($Type == 'earning') ? get_option( 'rewards_dynamic_rule_purchase_history' ) : get_option( 'rewards_dynamic_rule_purchase_history_redeem' ) ;
            if ( ! srp_check_is_array( $Rules ) )
                return $Percentage ;

            global $wpdb ;
            $Total    = array() ;
            $OrderIds = $wpdb->get_results( $wpdb->prepare( "SELECT posts.ID
			FROM $wpdb->posts as posts
			LEFT JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
			WHERE   meta.meta_key       = '_customer_user'
			AND     posts.post_type     IN ('" . implode( "','" , wc_get_order_types( 'order-count' ) ) . "')
			AND     posts.post_status   IN ('" . implode( "','" , array_keys( wc_get_order_statuses() ) ) . "')
			AND     meta_value          = %d
		" , $UserId ) , ARRAY_A ) ;
            if ( srp_check_is_array( $OrderIds ) ) {
                foreach ( $OrderIds as $Ids ) {
                    $Total[] = get_post_meta( $Ids[ 'ID' ] , '_order_total' , true ) ;
                }
            }

            $NewArr     = array() ;
            $OrderTotal = array_sum( $Total ) ;

            foreach ( $Rules as $Rule ) {
                if ( $Rule[ 'type' ] == '1' ) {
                    if ( $Type == 'earning' ) {
                        $BoolValue = ( get_option( 'rs_product_purchase_history_range' ) == '1' ) ? (count( $OrderIds ) <= $Rule[ 'value' ]) : (count( $OrderIds ) >= $Rule[ 'value' ]) ;
                    } else {
                        $BoolValue = (count( $OrderIds ) <= $Rule[ 'value' ]) ;
                    }
                    if ( $BoolValue )
                        $NewArr[ $Rule[ "value" ] ] = $Rule[ "percentage" ] ;
                }
                if ( $Rule[ 'type' ] == '2' ) {
                    if ( $Type == 'earning' ) {
                        $BoolValue = ( get_option( 'rs_product_purchase_history_range' ) == '1' ) ? ($OrderTotal <= $Rule[ 'value' ]) : ($OrderTotal >= $Rule[ 'value' ]) ;
                    } else {
                        $BoolValue = ($OrderTotal <= $Rule[ 'value' ]) ;
                    }
                    if ( $BoolValue )
                        $NewArr[ $Rule[ "value" ] ] = $Rule[ "percentage" ] ;
                }
            }

            if ( ! srp_check_is_array( $NewArr ) )
                return $Percentage ;

            if ( get_option( 'rs_product_purchase_history_range' ) == '2' ) {
                $MaxValue         = max( array_keys( $NewArr ) ) ;
                $PointsPercentage = $NewArr[ $MaxValue ] ;
            } else {
                $MinValue         = min( array_keys( $NewArr ) ) ;
                $PointsPercentage = $NewArr[ $MinValue ] ;
            }

            $Priority = ($Type == 'earning') ? get_option( 'rs_choose_priority_level_selection' ) : get_option( 'rs_choose_priority_level_selection_for_redeem' ) ;
            if ( $Priority == '1' ) {
                $Percentage = ($BoolVal == 'no') ? (($Percentage >= $PointsPercentage) ? $Percentage : $PointsPercentage) : $PointsPercentage ;
            } else {
                $Percentage = ($BoolVal == 'no') ? (($Percentage <= $PointsPercentage) ? $Percentage : $PointsPercentage) : $PointsPercentage ;
            }

            return $Percentage ;
        }

        /* Percentage based on Earned Points */

        public static function points_percentage( $Rules , $Points , $Percentage , $Type , $BoolVal ) {
            if ( ! srp_check_is_array( $Rules ) )
                return $Percentage ;

            $NewArr = array() ;
            foreach ( $Rules as $Rule ) {
                if ( get_option( 'rs_free_product_range' ) == '2' ) {
                    if ( $Rule[ "rewardpoints" ] <= $Points )
                        $NewArr[ $Rule[ "rewardpoints" ] ] = $Rule[ "percentage" ] ;
                } else {
                    if ( $Rule[ "rewardpoints" ] >= $Points )
                        $NewArr[ $Rule[ "rewardpoints" ] ] = $Rule[ "percentage" ] ;
                }
            }

            if ( ! srp_check_is_array( $NewArr ) )
                return $Percentage ;

            if ( get_option( 'rs_free_product_range' ) == '2' ) {
                $MaxValue         = max( array_keys( $NewArr ) ) ;
                $PointsPercentage = $NewArr[ $MaxValue ] ;
            } else {
                $MinValue         = min( array_keys( $NewArr ) ) ;
                $PointsPercentage = $NewArr[ $MinValue ] ;
            }

            $Priority = ($Type == 'earning') ? get_option( 'rs_choose_priority_level_selection' ) : get_option( 'rs_choose_priority_level_selection_for_redeem' ) ;
            if ( $Priority == '1' ) {
                $Percentage = ($BoolVal == 'no') ? (($Percentage >= $PointsPercentage) ? $Percentage : $PointsPercentage) : $PointsPercentage ;
            } else {
                $Percentage = ($BoolVal == 'no') ? (($Percentage <= $PointsPercentage) ? $Percentage : $PointsPercentage) : $PointsPercentage ;
            }
            return $Percentage ;
        }

    }

}