<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSMemberFunction' ) ) {

	/**
	 *  Class RSMemberFunction.
	 */
	class RSMemberFunction {

		/**
		 *  Redeeming Percentage.
		 *
		 * @param int $user_id User ID.
		 */
		public static function redeem_points_percentage( $user_id ) {
			$point_value = wc_format_decimal( get_option( 'rs_redeem_point_value' ) );

			if ( 'yes' !== get_option( 'rs_redeeming_activated' ) ) {
				return $point_value;
			}

			if ( empty( $user_id ) ) {
				return $point_value;
			}

			$user_obj                         = new WP_User( $user_id );
			$user_role                        = $user_obj->roles;
			$role_of_user                     = isset( $user_role[0] ) ? $user_role[0] : '';
			$points_data                      = new RS_Points_Data( $user_id );
			$points                           = ( '1' === get_option( 'rs_select_redeem_points_based_on' ) ) ? $points_data->total_earned_points() : $points_data->total_available_points();
			$redeem_level_based_on_user_role  = get_option( 'rs_enable_user_role_based_reward_points_for_redeem' );
			$redeem_level_based_on_points     = get_option( 'rs_enable_redeem_level_based_reward_points' );
			$redeem_level_based_on_history    = get_option( 'rs_enable_user_purchase_history_based_reward_points_redeem' );
			$redeem_level_based_on_membership = class_exists( 'SUMOMemberships' ) ? get_option( 'rs_enable_membership_plan_based_redeem' ) : 'no';
			/* UserRole Level Enabled */
			if ( ( 'yes' === $redeem_level_based_on_user_role ) && ( 'yes' !== $redeem_level_based_on_points ) && ( 'yes' !== $redeem_level_based_on_membership ) ) {
				$role_percentage = ( '' !== get_option( 'rs_reward_user_role_for_redeem_' . $role_of_user ) ) ? get_option( 'rs_reward_user_role_for_redeem_' . $role_of_user ) : 100;
				$percentage      = ( 'yes' === $redeem_level_based_on_history ) ? self::purchase_history_percentage( $user_id, $role_percentage, 'redeem' ) : $role_percentage;
				$percent_value   = ( $point_value * (float) $percentage ) / 100;
				return $percent_value;
			}

			/* Earning Level Enabled */
			if ( ( 'yes' !== $redeem_level_based_on_user_role ) && ( 'yes' === $redeem_level_based_on_points ) && ( 'yes' !== $redeem_level_based_on_membership ) ) {
				$rules             = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_for_redeem' ), 'rewardpoints' );
				$points_percentage = self::points_percentage( $rules, $points, 100, 'redeem', 'yes' );
				$percentage        = ( 'yes' === $redeem_level_based_on_history ) ? self::purchase_history_percentage( $user_id, $points_percentage, 'redeem' ) : $points_percentage;
				$percent_value     = ( $point_value * (float) $percentage ) / 100;
				return $percent_value;
			}

			/* Membership Level Enabled */
			if ( ( 'yes' !== $redeem_level_based_on_user_role ) && ( 'yes' !== $redeem_level_based_on_points ) && ( 'yes' === $redeem_level_based_on_membership ) ) {
				$membership_percentage = self::sumo_membership_percentage( $user_id, 100, 'redeem', 'yes' );
				$percentage            = ( 'yes' === $redeem_level_based_on_history ) ? self::purchase_history_percentage( $user_id, $membership_percentage, 'redeem' ) : $membership_percentage;
				$percent_value         = ( $point_value * (float) $percentage ) / 100;
				return $percent_value;
			}

			/* All Level Enabled */
			if ( ( 'yes' === $redeem_level_based_on_user_role ) && ( 'yes' === $redeem_level_based_on_points ) && ( 'yes' === $redeem_level_based_on_membership ) ) {
				$rules                 = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_for_redeem' ), 'rewardpoints' );
				$role_percentage       = ( '' !== get_option( 'rs_reward_user_role_for_redeem_' . $role_of_user ) ) ? get_option( 'rs_reward_user_role_for_redeem_' . $role_of_user ) : 100;
				$points_percentage     = self::points_percentage( $rules, $points, $role_percentage, 'redeem', 'no' );
				$membership_percentage = self::sumo_membership_percentage( $user_id, $points_percentage, 'redeem', 'no' );
				$percentage            = ( 'yes' === $redeem_level_based_on_history ) ? self::purchase_history_percentage( $user_id, $membership_percentage, 'redeem' ) : $membership_percentage;
				$percent_value         = ( $point_value * (float) $percentage ) / 100;
				return $percent_value;
			}

			/* UserRole Level Disabled */
			if ( ( 'yes' !== $redeem_level_based_on_user_role ) && ( 'yes' === $redeem_level_based_on_points ) && ( 'yes' === $redeem_level_based_on_membership ) ) {
				$rules                 = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_for_redeem' ), 'rewardpoints' );
				$points_percentage     = self::points_percentage( $rules, $points, 100, 'redeem', 'no' );
				$membership_percentage = self::sumo_membership_percentage( $user_id, $points_percentage, 'redeem', 'no' );
				$percentage            = ( 'yes' === $redeem_level_based_on_history ) ? self::purchase_history_percentage( $user_id, $membership_percentage, 'redeem' ) : $membership_percentage;
				$percent_value         = ( $point_value * $percentage ) / 100;
				return $percent_value;
			}

			/* Earning Level Disabled */
			if ( ( 'yes' === $redeem_level_based_on_user_role ) && ( 'yes' !== $redeem_level_based_on_points ) && ( 'yes' === $redeem_level_based_on_membership ) ) {
				$role_percentage       = '' !== get_option( 'rs_reward_user_role_for_redeem_' . $role_of_user ) ? get_option( 'rs_reward_user_role_for_redeem_' . $role_of_user ) : 100;
				$membership_percentage = self::sumo_membership_percentage( $user_id, $role_percentage, 'redeem', 'no' );
				$percentage            = ( 'yes' === $redeem_level_based_on_history ) ? self::purchase_history_percentage( $user_id, $membership_percentage, 'redeem' ) : $membership_percentage;
				$percent_value         = ( $point_value * (float) $percentage ) / 100;
				return $percent_value;
			}

			/* Membership Level Disabled */
			if ( ( 'yes' === $redeem_level_based_on_user_role ) && ( 'yes' === $redeem_level_based_on_points ) && ( 'yes' !== $redeem_level_based_on_membership ) ) {
				$rules             = multi_dimensional_sort( get_option( 'rewards_dynamic_rule_for_redeem' ), 'rewardpoints' );
				$role_percentage   = '' !== get_option( 'rs_reward_user_role_for_redeem_' . $role_of_user ) ? get_option( 'rs_reward_user_role_for_redeem_' . $role_of_user ) : 100;
				$points_percentage = self::points_percentage( $rules, $points, $role_percentage, 'redeem', 'no' );
				$percentage        = ( 'yes' === $redeem_level_based_on_history ) ? self::purchase_history_percentage( $user_id, $points_percentage, 'redeem' ) : $points_percentage;
				$percent_value     = ( $point_value * (float) $percentage ) / 100;
				return $percent_value;
			}

			/* All Level Disabled */
			if ( ( 'yes' !== $redeem_level_based_on_user_role ) && ( 'yes' !== $redeem_level_based_on_points ) && ( 'yes' !== $redeem_level_based_on_membership ) ) {
				$percentage    = ( 'yes' === $redeem_level_based_on_history ) ? self::purchase_history_percentage( $user_id, 100, 'redeem', 'yes' ) : 100;
				$percent_value = ( $point_value * (float) $percentage ) / 100;
				return $percent_value;
			}
		}

		/**
		 * Earning Percentage
		 */
		public static function earn_points_percentage( $user_id, $points ) {
			if ( empty( $user_id ) ) {
				$percentage = ( $points * 100 ) / 100;
				return $percentage;
			}

			$user_obj                      = new WP_User( $user_id );
			$user_role                     = $user_obj->roles;
			$user_role_based_on_percentage = self::get_user_role_based_on_percentage( $user_role );
						$selected_role     = isset( $user_role[0] ) ? $user_role[0] : '';
			$role_of_user                  = ! empty( $user_role_based_on_percentage ) ? $user_role_based_on_percentage : $selected_role;
			$points_data                   = new RS_Points_Data( $user_id );
			$TotalPoints                   = '1' == get_option( 'rs_select_earn_points_based_on' ) ? $points_data->total_earned_points() : $points_data->total_available_points();
			$EarnLevelBasedonUserRole      = get_option( 'rs_enable_user_role_based_reward_points' );
			$EarnLevelBasedonPoints        = get_option( 'rs_enable_earned_level_based_reward_points' );
			$EarnLevelBasedonMembership    = class_exists( 'SUMOMemberships' ) ? get_option( 'rs_enable_membership_plan_based_reward_points' ) : 'no';
			$EarnLevelBasedonHistory       = get_option( 'rs_enable_user_purchase_history_based_reward_points' );

			/* UserRole Level Enabled */
			if ( ( 'yes' == $EarnLevelBasedonUserRole ) && ( 'yes' != $EarnLevelBasedonPoints ) && ( 'yes' != $EarnLevelBasedonMembership ) ) {
				$role_percentage = '' != get_option( 'rs_reward_user_role_' . $role_of_user ) ? get_option( 'rs_reward_user_role_' . $role_of_user ) : 100;
				$percentage      = ( 'yes' == $EarnLevelBasedonHistory ) ? self::purchase_history_percentage( $user_id, $role_percentage, 'earning' ) : $role_percentage;
				$percentage      = ( $points * (float) $percentage ) / 100;
				$percentage      = self::promotional_points( $percentage );
				return $percentage;
			}

			/* Earning Level Enabled */
			if ( ( 'yes' != $EarnLevelBasedonUserRole ) && ( 'yes' == $EarnLevelBasedonPoints ) && ( 'yes' != $EarnLevelBasedonMembership ) ) {
				$rules             = multi_dimensional_sort( get_option( 'rewards_dynamic_rule' ), 'rewardpoints' );
				$points_percentage = self::points_percentage( $rules, $TotalPoints, 100, 'earning', 'yes' );
				$percentage        = ( 'yes' == $EarnLevelBasedonHistory ) ? self::purchase_history_percentage( $user_id, $points_percentage, 'earning' ) : $points_percentage;
				$percentage        = ( $points * (float) $percentage ) / 100;
				$percentage        = self::promotional_points( $percentage );
				return $percentage;
			}

			/* Membership Level Enabled */
			if ( ( 'yes' != $EarnLevelBasedonUserRole ) && ( 'yes' != $EarnLevelBasedonPoints ) && ( 'yes' == $EarnLevelBasedonMembership ) ) {
				$membership_percentage = self::sumo_membership_percentage( $user_id, 100, 'earning', 'yes' );
				$percentage            = ( 'yes' == $EarnLevelBasedonHistory ) ? self::purchase_history_percentage( $user_id, $membership_percentage, 'earning' ) : $membership_percentage;
				$percentage            = ( $points * (float) $percentage ) / 100;
				$percentage            = self::promotional_points( $percentage );
				return $percentage;
			}

			/* All Level Enabled */
			if ( ( 'yes' == $EarnLevelBasedonUserRole ) && ( 'yes' == $EarnLevelBasedonPoints ) && ( 'yes' == $EarnLevelBasedonMembership ) ) {
				$rules                 = multi_dimensional_sort( get_option( 'rewards_dynamic_rule' ), 'rewardpoints' );
				$role_percentage       = '' != get_option( 'rs_reward_user_role_' . $role_of_user ) ? get_option( 'rs_reward_user_role_' . $role_of_user ) : 100;
				$points_percentage     = self::points_percentage( $rules, $TotalPoints, $role_percentage, 'earning', 'no' );
				$membership_percentage = self::sumo_membership_percentage( $user_id, $points_percentage, 'earning', 'no' );
				$percentage            = ( 'yes' == $EarnLevelBasedonHistory ) ? self::purchase_history_percentage( $user_id, $membership_percentage, 'earning' ) : $membership_percentage;
				$percentage            = ( $points * (float) $percentage ) / 100;
				$percentage            = self::promotional_points( $percentage );
				return $percentage;
			}

			/* UserRole Level Disabled */
			if ( ( 'yes' != $EarnLevelBasedonUserRole ) && ( 'yes' == $EarnLevelBasedonPoints ) && ( 'yes' == $EarnLevelBasedonMembership ) ) {
				$rules                 = multi_dimensional_sort( get_option( 'rewards_dynamic_rule' ), 'rewardpoints' );
				$points_percentage     = self::points_percentage( $rules, $TotalPoints, 100, 'earning', 'no' );
				$membership_percentage = self::sumo_membership_percentage( $user_id, $points_percentage, 'earning', 'no' );
				$percentage            = ( 'yes' == $EarnLevelBasedonHistory ) ? self::purchase_history_percentage( $user_id, $membership_percentage, 'earning' ) : $membership_percentage;
				$percentage            = ( $points * (float) $percentage ) / 100;
				$percentage            = self::promotional_points( $percentage );
				return $percentage;
			}

			/* Earning Level Disabled */
			if ( ( 'yes' == $EarnLevelBasedonUserRole ) && ( 'yes' != $EarnLevelBasedonPoints ) && ( 'yes' == $EarnLevelBasedonMembership ) ) {
				$role_percentage       = '' != get_option( 'rs_reward_user_role_' . $role_of_user ) ? get_option( 'rs_reward_user_role_' . $role_of_user ) : 100;
				$membership_percentage = self::sumo_membership_percentage( $user_id, $role_percentage, 'earning', 'no' );
				$percentage            = ( 'yes' == $EarnLevelBasedonHistory ) ? self::purchase_history_percentage( $user_id, $membership_percentage, 'earning' ) : $membership_percentage;
				$percentage            = ( $points * (float) $percentage ) / 100;
				$percentage            = self::promotional_points( $percentage );
				return $percentage;
			}

			/* Membership Level Disabled */
			if ( ( 'yes' == $EarnLevelBasedonUserRole ) && ( 'yes' == $EarnLevelBasedonPoints ) && ( 'yes' != $EarnLevelBasedonMembership ) ) {
				$rules             = multi_dimensional_sort( get_option( 'rewards_dynamic_rule' ), 'rewardpoints' );
				$role_percentage   = get_option( 'rs_reward_user_role_' . $role_of_user ) != '' ? get_option( 'rs_reward_user_role_' . $role_of_user ) : 100;
				$points_percentage = self::points_percentage( $rules, $TotalPoints, $role_percentage, 'earning', 'no' );
				$percentage        = ( 'yes' == $EarnLevelBasedonHistory ) ? self::purchase_history_percentage( $user_id, $points_percentage, 'earning' ) : $points_percentage;
				$percentage        = ( $points * (float) $percentage ) / 100;
				$percentage        = self::promotional_points( $percentage );
				return $percentage;
			}

			/* All Level Disabled */
			if ( ( 'yes' != $EarnLevelBasedonUserRole ) && ( 'yes' != $EarnLevelBasedonPoints ) && ( 'yes' != $EarnLevelBasedonMembership ) ) {
				$percentage = ( 'yes' == $EarnLevelBasedonHistory ) ? self::purchase_history_percentage( $user_id, 100, 'earning', 'yes' ) : 100;
				$percentage = ( $points * (float) $percentage ) / 100;
				$percentage = self::promotional_points( $percentage );
				return $percentage;
			}
		}

		/**
		 * Get user role based on percentage
		 */
		public static function get_user_role_based_on_percentage( $roles ) {

			if ( ! srp_check_is_array( $roles ) ) {
				return '';
			}

			$user_role_data = array();
			foreach ( $roles as $role_key => $role_value ) {
				$percentage = get_option( 'rs_reward_user_role_' . sanitize_title( $role_value ) );
				if ( ! $percentage ) {
					continue;
				}

				$user_role_data[ $role_value ] = $percentage;
			}

			if ( ! srp_check_is_array( $user_role_data ) ) {
				return '';
			}

			$role_percentage = max( array_values( $user_role_data ) );

			return array_search( $role_percentage, $user_role_data, true );
		}

		/**
		 * Percentage based on SUMO Membership
		 */
		public static function sumo_membership_percentage( $user_id, $percentage, $Type, $BoolVal ) {
			$membership_percentage = array();
			$args                  = array(
				'post_type'  => 'sumomembers',
				'meta_query' => array(
					array(
						'key'     => 'sumomemberships_userid',
						'value'   => array( $user_id ),
						'compare' => 'IN',
					),
				),
			);
			$Posts                 = get_posts( $args );
			if ( ! isset( $Posts[0]->ID ) ) {
				return $percentage;
			}

			$PostId = $Posts[0]->ID;
			$Plans  = get_post_meta( $PostId, 'sumomemberships_saved_plans', true );
			if ( ! srp_check_is_array( $Plans ) ) {
				return $percentage;
			}

			foreach ( $Plans as $Plan ) {
				if ( ! isset( $Plan['choose_plan'] ) && empty( $Plan['choose_plan'] ) ) {
					$membership_percentage[] = 100;
				}

				$PlanId                  = $Plan['choose_plan'];
				$PlanPercentage          = ( 'earning' == $Type ) ? get_option( 'rs_reward_membership_plan_' . $PlanId ) : get_option( 'rs_reward_membership_plan_for_redeem' . $PlanId );
				$membership_percentage[] = ! empty( $PlanPercentage ) ? $PlanPercentage : 100;
			}
			if ( ! srp_check_is_array( $membership_percentage ) ) {
				return $percentage;
			}

			$Priority = ( 'earning' == $Type ) ? get_option( 'rs_choose_priority_level_selection' ) : get_option( 'rs_choose_priority_level_selection_for_redeem' );
			if ( '1' == $Priority ) {
				$percentage = ( 'no' == $BoolVal ) ? ( ( $percentage >= max( $membership_percentage ) ) ? $percentage : max( $membership_percentage ) ) : max( $membership_percentage );
			} else {
				$percentage = ( 'no' == $BoolVal ) ? ( ( $percentage <= min( $membership_percentage ) ) ? $percentage : min( $membership_percentage ) ) : min( $membership_percentage );
			}
			return $percentage;
		}

		/**
		 * Percentage based on Purcahsed History
		 */
		public static function purchase_history_percentage( $user_id, $percentage, $Type, $BoolVal = 'no' ) {
			$rules = ( 'earning' == $Type ) ? get_option( 'rewards_dynamic_rule_purchase_history' ) : get_option( 'rewards_dynamic_rule_purchase_history_redeem' );
			if ( ! srp_check_is_array( $rules ) ) {
				return $percentage;
			}

			$purchase_history_order_statuses        = array();
			$purchase_history_selected_order_status = get_option( 'rs_earning_percentage_order_status_control', array( 'completed' ) );
			foreach ( $purchase_history_selected_order_status as $order_status ) {
				$purchase_history_order_statuses[] = 'wc-' . $order_status;
			}

			global $wpdb;
			$Total = array();
						/**
						 * Hook:rs_purchase_history_where_query.
						 *
						 * @since 1.0
						 */
			$where = apply_filters( 'rs_purchase_history_where_query', '' );

			$db       = &$wpdb;
			$OrderIds = $db->get_results(
				$db->prepare(
					"SELECT posts.ID
			FROM $db->posts as posts
			LEFT JOIN {$db->postmeta} AS meta ON posts.ID = meta.post_id
			WHERE   meta.meta_key       = '_customer_user'
			AND     posts.post_type     = 'shop_order'
			AND     posts.post_status   IN ('" . implode( "','", $purchase_history_order_statuses ) . "')
			AND     meta_value          = %d
                        $where",
					get_current_user_id()
				),
				ARRAY_A
			);

			if ( srp_check_is_array( $OrderIds ) ) {
				foreach ( $OrderIds as $Ids ) {
					$Total[] = get_post_meta( $Ids['ID'], '_order_total', true );
				}
			}

			$NewArr     = array();
			$OrderTotal = array_sum( $Total );

			foreach ( $rules as $Rule ) {
				if ( '1' == $Rule['type'] ) {
					if ( 'earning' == $Type ) {
						$BoolValue = ( '1' == get_option( 'rs_product_purchase_history_range' ) ) ? ( count( $OrderIds ) <= $Rule['value'] ) : ( count( $OrderIds ) >= $Rule['value'] );
					} else {
						$BoolValue = ( count( $OrderIds ) <= $Rule['value'] );
					}
					if ( $BoolValue ) {
						$NewArr[ $Rule['value'] ] = $Rule['percentage'];
					}
				}
				if ( '2' == $Rule['type'] ) {
					if ( 'earning' == $Type ) {
						$BoolValue = ( '1' == get_option( 'rs_product_purchase_history_range' ) ) ? ( $OrderTotal <= $Rule['value'] ) : ( $OrderTotal >= $Rule['value'] );
					} else {
						$BoolValue = ( $OrderTotal <= $Rule['value'] );
					}
					if ( $BoolValue ) {
						$NewArr[ $Rule['value'] ] = $Rule['percentage'];
					}
				}
			}

			if ( ! srp_check_is_array( $NewArr ) ) {
				return $percentage;
			}

			if ( '2' == get_option( 'rs_product_purchase_history_range' ) ) {
				$MaxValue          = max( array_keys( $NewArr ) );
				$points_percentage = $NewArr[ $MaxValue ];
			} else {
				$MinValue          = min( array_keys( $NewArr ) );
				$points_percentage = $NewArr[ $MinValue ];
			}

			$Priority = ( 'earning' == $Type ) ? get_option( 'rs_choose_priority_level_selection' ) : get_option( 'rs_choose_priority_level_selection_for_redeem' );
			if ( '1' == $Priority ) {
				$percentage = ( 'no' == $BoolVal ) ? ( ( $percentage >= $points_percentage ) ? $percentage : $points_percentage ) : $points_percentage;
			} else {
				$percentage = ( 'no' == $BoolVal ) ? ( ( $percentage <= $points_percentage ) ? $percentage : $points_percentage ) : $points_percentage;
			}

			return $percentage;
		}

		/**
		 * Percentage based on Earned Points
		 */
		public static function points_percentage( $rules, $points, $percentage, $Type, $BoolVal ) {
			if ( ! srp_check_is_array( $rules ) ) {
				return $percentage;
			}

			$NewArr = array();
			foreach ( $rules as $Rule ) {
				if ( '2' == get_option( 'rs_free_product_range' ) ) {
					if ( $Rule['rewardpoints'] <= $points ) {
						$NewArr[ $Rule['rewardpoints'] ] = $Rule['percentage'];
					}
				} elseif ( $Rule['rewardpoints'] >= $points ) {
						$NewArr[ $Rule['rewardpoints'] ] = $Rule['percentage'];
				}
			}

			if ( ! srp_check_is_array( $NewArr ) ) {
				return $percentage;
			}

			if ( '2' == get_option( 'rs_free_product_range' ) ) {
				$MaxValue          = max( array_keys( $NewArr ) );
				$points_percentage = $NewArr[ $MaxValue ];
			} else {
				$MinValue          = min( array_keys( $NewArr ) );
				$points_percentage = $NewArr[ $MinValue ];
			}

			$Priority = ( 'earning' == $Type ) ? get_option( 'rs_choose_priority_level_selection' ) : get_option( 'rs_choose_priority_level_selection_for_redeem' );
			if ( '1' == $Priority ) {
				$percentage = ( 'no' == $BoolVal ) ? ( ( $percentage >= $points_percentage ) ? $percentage : $points_percentage ) : $points_percentage;
			} else {
				$percentage = ( 'no' == $BoolVal ) ? ( ( $percentage <= $points_percentage ) ? $percentage : $points_percentage ) : $points_percentage;
			}
			return $percentage;
		}

		/**
		 * Promotional Reward Points
		 */
		public static function promotional_points( $percentage ) {

			if ( 'yes' != get_option( 'rs_promotional_points_activated' ) ) {
				return $percentage;
			}

			$rule_ids = srp_get_rule_ids();

			if ( ! srp_check_is_array( $rule_ids ) ) {
				return $percentage;
			}

			$promotional_points = array();

			foreach ( $rule_ids as $rule_id ) {

				$rule = srp_get_rule( $rule_id );

				if ( ! is_object( $rule ) ) {
					continue;
				}

				if ( 'yes' != $rule->get_enable() ) {
					continue;
				}

				$from_time = SRP_Date_Time::get_mysql_date_time_format( $rule->get_from_date() . ' 12:00:00AM', false, 'UTC' );
				$to_time   = SRP_Date_Time::get_mysql_date_time_format( $rule->get_to_date() . ' 11:59:59PM', false, 'UTC' );
				if ( ! ( ( time() >= strtotime( $from_time ) ) && ( time() <= strtotime( $to_time ) ) ) ) {
						continue;
				}

				$promotional_points[] = $rule->get_point();
			}

			if ( ! srp_check_is_array( $promotional_points ) ) {
				return $percentage;
			}

			$promotional_point = reset( $promotional_points );
			$percentage        = $promotional_point * $percentage;

			return $percentage;
		}
	}

}
