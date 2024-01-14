<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 *  Handles the Bonus Points.
 * */
if ( ! class_exists( 'Bonus_Points_Handler' ) ) {

	/**
	 * Class.
	 * */
	class Bonus_Points_Handler {

		/**
		 * Current Order ID.
		 * */
		protected static $current_order_id;

		/**
		 * Class Initialization.
		 * */
		public static function init() {

			$callbacks = array(
				'may_be_add_bonus_for_orders_placed_with_repeat',
				'may_be_add_bonus_for_orders_placed_without_repeat',
			);

			foreach ( $callbacks as $callback_name ) {
				add_action( 'woocommerce_order_status_completed', array( __CLASS__, sanitize_title( $callback_name ) ), 11 );
			}

			// Get where query in order count.
			add_filter( 'rs_where_query_in_order_count', array( __CLASS__, 'get_where_query_in_order_count' ), 10, 2 );
			// Points data before insertion
			add_filter( 'rs_points_data_before_insertion', array( __CLASS__, 'points_data_before_insertion' ), 9999 );
		}

		/**
		 * Add bonus points based on number of orders placed on with repeat type.
		 *
		 * @param int $order_id Order ID.
		 * */
		public static function may_be_add_bonus_for_orders_placed_with_repeat( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			$user_id = $order->get_user_id();
			if ( ! $user_id ) {
				return;
			}

			if ( ! allow_reward_points_for_user( $user_id ) ) {
				return;
			}

			$banning_type = check_banning_type( $user_id );
			if ( 'earningonly' === $banning_type || 'both' === $banning_type ) {
				return;
			}

			if ( 'yes' === $order->get_meta( 'rs_bonus_for_orders_with_repeat_awarded_once' ) ) {
				return;
			}

			if ( 'yes' !== get_option( 'rs_bonus_points_activated' ) || 'yes' !== get_option( 'rs_enable_bonus_point_for_orders' ) ) {
				return;
			}

			if ( '2' === get_option( 'rs_bonus_points_rules_for_orders_type', 1 ) ) {
				return;
			}

			$settings_orders_value = absint( get_option( 'rs_bonus_points_number_of_orders_with_repeat' ) );
			$point                 = absint( get_option( 'rs_bonus_points_value_number_of_orders_with_repeat' ) );
			if ( ! $settings_orders_value || ! $point ) {
				return;
			}

			$args = array(
				'from_date' => get_option( 'rs_bonus_points_from_date_number_of_orders_with_repeat' ),
				'to_date'   => get_option( 'rs_bonus_points_to_date_number_of_orders_with_repeat' ),
				'meta_key'  => 'rs_recorded_order_no_of_orders_type',
			);

			$order_ids   = self::get_user_placed_order_ids( $user_id, $args );
			$order_count = count( $order_ids );
			if ( ! $order_count ) {
				return;
			}

			$bonus_points         = 0;
			$settings_order_count = 0;
			for ( $i = $settings_orders_value; $i <= $order_count; $i += $settings_orders_value ) {
				$bonus_points         += $point;
				$settings_order_count += $settings_orders_value;
			}

			if ( ! $bonus_points ) {
				return;
			}

			self::$current_order_id = $order_id;
			$enabledisablemaxpoints = get_option( 'rs_enable_disable_max_earning_points_for_user' );
			$new_obj                = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' == $enabledisablemaxpoints ) {
				$new_obj->check_point_restriction( $bonus_points, 0, 'OBP', $user_id, '', '', '', '', '' );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $bonus_points,
					'event_slug'        => 'OBP', /* Order Bonus Points */
					'user_id'           => $user_id,
					'orderid'           => $order_id,
					'totalearnedpoints' => $bonus_points,
				);

				$new_obj->total_points_management( $valuestoinsert, false );
			}

			// Update bonus point meta.
			$order->update_meta_data( 'rs_bonus_points_for_orders_with_repeat', $bonus_points );
			// Update meta to check occur once.
			$order->update_meta_data( 'rs_bonus_for_orders_with_repeat_awarded_once', 'yes' );
			$order->save();

			// Record order meta for selected orders.
			self::record_order_meta_for_selected_orders( $settings_order_count, $order_ids );
		}

		/**
		 * Add bonus points based on number of orders placed on without repeat type.
		 *
		 * @param int $order_id Order ID.
		 * */
		public static function may_be_add_bonus_for_orders_placed_without_repeat( $order_id ) {

			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			if ( 'yes' === $order->get_meta( 'rs_bonus_for_orders_without_repeat_awarded_once' ) ) {
				return;
			}

			$user_id = $order->get_user_id();
			if ( ! $user_id ) {
				return;
			}

			if ( ! allow_reward_points_for_user( $user_id ) ) {
				return;
			}

			$banning_type = check_banning_type( $user_id );
			if ( 'earningonly' === $banning_type || 'both' === $banning_type ) {
				return;
			}

			if ( 'yes' !== get_option( 'rs_bonus_points_activated' ) || 'yes' !== get_option( 'rs_enable_bonus_point_for_orders' ) ) {
				return;
			}

			if ( '1' === get_option( 'rs_bonus_points_rules_for_orders_type', 1 ) ) {
				return;
			}

			$rules = get_option( 'rs_bonus_points_number_of_orders_without_repeat_rules', array() );
			if ( ! srp_check_is_array( array_filter( $rules ) ) ) {
				return;
			}

			self::$current_order_id = $order_id;
			$bonus_points           = 0;
			$saved_rules            = array();
			$order_counts           = array();
			foreach ( $rules as $rule_key => $rule_value ) {

				$level_name       = isset( $rule_value['level_name'] ) ? $rule_value['level_name'] : '';
				$number_of_orders = isset( $rule_value['number_of_orders'] ) ? absint( $rule_value['number_of_orders'] ) : 0;
				$points           = isset( $rule_value['bonus_points'] ) ? absint( $rule_value['bonus_points'] ) : 0;
				if ( ! $level_name || ! $number_of_orders || ! $points ) {
					continue;
				}

				if ( ! self::validate_orders_without_repeating( $rule_key, $rule_value, $user_id ) ) {
					continue;
				}

				$args = array(
					'from_date' => isset( $rule_value['from_date'] ) ? $rule_value['from_date'] : '',
					'to_date'   => isset( $rule_value['to_date'] ) ? $rule_value['to_date'] : '',
					'meta_key'  => 'rs_recorded_order_no_of_orders_type',
				);

				$order_ids   = self::get_user_placed_order_ids( $user_id, $args );
				$order_count = count( $order_ids );
				if ( ! $order_count ) {
					continue;
				}

				if ( $order_count < $number_of_orders ) {
					continue;
				}

				$order_counts[] = $order_count;

				$bonus_points += $points;

				$saved_rules[ $rule_key ] = $rule_value;

				// Record order meta for selected orders.
				self::record_order_meta_for_selected_orders( $number_of_orders, $order_ids );
			}

			if ( ! $bonus_points ) {
				return;
			}

			$enabledisablemaxpoints = get_option( 'rs_enable_disable_max_earning_points_for_user' );
			$new_obj                = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' === $enabledisablemaxpoints ) {
				$new_obj->check_point_restriction( $bonus_points, 0, 'OBP', $user_id, '', '', '', '', '' );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $bonus_points,
					'event_slug'        => 'OBP', /* Order Bonus Points */
					'user_id'           => $user_id,
					'orderid'           => $order_id,
					'totalearnedpoints' => $bonus_points,
				);
				$new_obj->total_points_management( $valuestoinsert, false );
			}

			$stored_rules = get_user_meta( $user_id, 'rs_stored_rules_on_no_of_orders_without_repeating', true );
			if ( srp_check_is_array( $stored_rules ) ) {
				$saved_rules = $stored_rules + $saved_rules;
			} else {
				$saved_rules = $saved_rules;
			}

			// Update rules in user meta.
			update_user_meta( $user_id, 'rs_stored_rules_on_no_of_orders_without_repeating', $saved_rules );
			// Update bonus point meta.
			$order->update_meta_data( 'rs_bonus_points_for_orders_without_repeat', $bonus_points );
			// Update order count meta.
			$order->update_meta_data( 'rs_orders_count_without_repeat', array_sum( $order_counts ) );
			// Update meta to check occur once.
			$order->update_meta_data( 'rs_bonus_for_orders_without_repeat_awarded_once', 'yes' );
			$order->save();
		}

		/**
		 * Validate order bonus points already awarded without repeating.
		 *
		 * @return int
		 * */
		public static function validate_orders_without_repeating( $settings_rule_key, $settings_rule_value, $user_id ) {

			$stored_rules = get_user_meta( $user_id, 'rs_stored_rules_on_no_of_orders_without_repeating', true );
			if ( ! srp_check_is_array( $stored_rules ) ) {
				return true;
			}

			$stored_rule = isset( $stored_rules[ $settings_rule_key ] ) ? $stored_rules[ $settings_rule_key ] : '';
			if ( ! srp_check_is_array( $stored_rule ) ) {
				return true;
			}

			$settings_level_name       = isset( $settings_rule_value['level_name'] ) ? $settings_rule_value['level_name'] : '';
			$settings_number_of_orders = isset( $settings_rule_value['number_of_orders'] ) ? $settings_rule_value['number_of_orders'] : 0;
			$settings_from_date        = isset( $settings_rule_value['from_date'] ) ? strtotime( $settings_rule_value['from_date'] ) : '';
			$settings_to_date          = isset( $settings_rule_value['to_date'] ) ? strtotime( $settings_rule_value['to_date'] ) : '';

			$stored_level_name       = isset( $stored_rule['level_name'] ) ? $stored_rule['level_name'] : '';
			$stored_number_of_orders = isset( $stored_rule['number_of_orders'] ) ? $stored_rule['number_of_orders'] : 0;
			$stored_from_date        = isset( $stored_rule['from_date'] ) ? strtotime( $stored_rule['from_date'] ) : '';
			$stored_to_date          = isset( $stored_rule['to_date'] ) ? strtotime( $stored_rule['to_date'] ) : '';

			if ( $settings_level_name == $stored_level_name && $settings_number_of_orders == $stored_number_of_orders && $settings_from_date == $stored_from_date && $settings_to_date == $stored_to_date ) {
				return false;
			}

			return true;
		}

		/**
		 * Get order data.
		 *
		 * @return mixed
		 * */
		public static function get_user_placed_order_ids( $user_id, $args ) {
						/**
						 * Hook:rs_where_query_in_order_count.
						 *
						 * @since 1.0
						 */
			$where = apply_filters( 'rs_where_query_in_order_count', '', $args );

			$meta_key = isset( $args['meta_key'] ) ? $args['meta_key'] : '';

			global $wpdb;
			$db        = &$wpdb;
			$order_ids = array();
			if ( $meta_key ) {
				$order_ids = $db->get_col(
					$db->prepare(
						"SELECT DISTINCT posts.ID
			FROM $db->posts as posts
			LEFT JOIN {$db->postmeta} AS meta ON posts.ID = meta.post_id
                        LEFT JOIN {$db->postmeta} AS meta1 ON posts.ID = meta1.post_id AND meta1.meta_key = '$meta_key'
                        WHERE   posts.post_type     = 'shop_order'
			AND     posts.post_status   = 'wc-completed'
                        AND     meta.meta_key       = '_customer_user'
			AND     meta.meta_value          = %d
                        AND     meta1.meta_key is null
                        " . $where . '
		',
						$user_id
					)
				);
			}

			return $order_ids;
		}

		/**
		 * Get where query in order count.
		 *
		 * @return string
		 * */
		public static function get_where_query_in_order_count( $where, $args ) {

			$from_date = ! empty( $args['from_date'] ) ? gmdate( 'Y-m-d', strtotime( $args['from_date'] ) ) : '';
			$to_date   = ! empty( $args['to_date'] ) ? gmdate( 'Y-m-d', strtotime( $args['to_date'] ) ) : '';

			if ( $from_date && $to_date ) {
				$where .= "AND posts.post_date_gmt BETWEEN '$from_date' AND '$to_date' ";
				return $where;
			}

			if ( $from_date ) {
				$where .= "AND posts.post_date_gmt >= '$from_date' ";
			}

			if ( $to_date ) {
				$where .= "AND posts.post_date_gmt <= '$to_date' ";
			}

			return $where;
		}

		/**
		 * Record order meta for selected orders.
		 *
		 * @return void
		 * */
		public static function record_order_meta_for_selected_orders( $order_count, $matched_order_ids ) {

			if ( ! srp_check_is_array( $matched_order_ids ) ) {
				return;
			}

			$sliced_order_ids = array_slice( $matched_order_ids, 0, $order_count );
			$order_ids        = array_map( array( __CLASS__, 'save_order_meta_number_of_orders_type' ), $sliced_order_ids );

			return array_filter( $order_ids );
		}

		/**
		 * Save order meta number of orders type.
		 *
		 * @return int
		 * */
		public static function save_order_meta_number_of_orders_type( $sliced_order_id ) {

			if ( ! $sliced_order_id ) {
				return 0;
			}

			$order = wc_get_order( $sliced_order_id );
			if ( 'yes' === $order->get_meta( 'rs_recorded_order_no_of_orders_type' ) ) {
				return 0;
			}

			$order->update_meta_data( 'rs_recorded_order_no_of_orders_type', 'yes' );

			if ( isset( self::$current_order_id ) ) {
				$order->update_meta_data( 'rs_bonus_awarded_order_id', self::$current_order_id );
			}

			$order->save();

			return $sliced_order_id;
		}

		/**
		 * Points data before insertion.
		 *
		 * @return array
		 * */
		public static function points_data_before_insertion( $table_args ) {

			if ( 'yes' != get_option( 'rs_bonus_points_activated' ) || 'yes' != get_option( 'rs_enable_bonus_point_for_orders' ) ) {
				return $table_args;
			}

			if ( ! isset( self::$current_order_id, $table_args['checkpoints'] ) || 'OBP' != $table_args['checkpoints'] ) {
				return $table_args;
			}

			if ( isset( $table_args['orderid'] ) ) {
				$table_args['orderid'] = self::$current_order_id;
			}

			return $table_args;
		}
	}

	Bonus_Points_Handler::init();
}
