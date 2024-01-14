<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 *  Handles the Bonus Points.
 * */
if ( ! class_exists( 'Anniversary_Points_Handler' ) ) {

	/**
	 * Class.
	 * */
	class Anniversary_Points_Handler {

		/**
		 * Class Initialization.
		 * */
		public static function init() {

			// Anniversary cron action.
			add_action( 'srp_anniversary_cron', array( __CLASS__, 'anniversary_cron_action' ) );
			// Add anniversary points on page load.
			add_action( 'wp_login', array( __CLASS__, 'add_anniversary_points_on_page_load' ), 10, 2 );

			// Render custom anniversary fields in registration.
			add_action( 'woocommerce_register_form', array( __CLASS__, 'render_custom_anniversary_fields' ) );
			// Render custom anniversary fields in edit account page.
			add_action( 'woocommerce_edit_account_form', array( __CLASS__, 'render_custom_anniversary_fields' ) );

			// Custom anniversary save.
			add_action( 'user_register', array( __CLASS__, 'custom_anniversary_save' ), 10, 1 );
			// Custom anniversary save.
			add_action( 'woocommerce_save_account_details', array( __CLASS__, 'custom_anniversary_save' ), 10, 1 );

			// Validate anniversary field on registration page.
			add_filter( 'woocommerce_registration_errors', array( __CLASS__, 'validate_anniversary_field' ), 12, 1 );
			// Validate anniversary field on edit account page.
			add_action( 'woocommerce_save_account_details_errors', array( __CLASS__, 'validate_anniversary_field_edit_account' ), 12, 1 );
			// Render anniversary field in checkout page.
			add_filter( 'woocommerce_checkout_fields', array( __CLASS__, 'anniversary_field_in_checkout' ) );

			// May be update anniversary field in order.
			add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'update_anniversary_field_in_order' ) );
			// May be add meta after points recorded.
			add_action( 'fp_reward_points_after_recorded', array( __CLASS__, 'may_be_add_meta_after_points_recorded' ), 10, 5 );
		}

		/**
		 *  Anniversary cron action.
		 */
		public static function anniversary_cron_action() {

			self::award_account_anniversary_points();

			self::award_custom_anniversary_points();
		}

		/**
		 *  Award account anniversary points.
		 */
		public static function award_account_anniversary_points( $user_id = false ) {

			if ( 'yes' != get_option( 'rs_anniversary_points_activated' ) || 'yes' != get_option( 'rs_enable_account_anniversary_point' ) ) {
				return;
			}

			$point_value              = get_option( 'rs_account_anniversary_point_value' );
			$account_anniversary_type = get_option( 'rs_account_anniversary_point_type', 'one_time' );
			if ( 'one_time' == $account_anniversary_type || 'every_year' == $account_anniversary_type ) {
				if ( ! $point_value ) {
					return;
				}

				if ( ! $user_id ) {
					$user_ids = self::get_matched_user_ids( 'account_anniv' );
					if ( ! srp_check_is_array( $user_ids ) ) {
						return;
					}
				} else {
					$user_ids = array( $user_id );
				}

				foreach ( $user_ids as $user_id ) {

					$user = get_user_by( 'ID', $user_id );
					if ( ! is_object( $user ) ) {
						continue;
					}

					if ( ! allow_reward_points_for_user( $user_id ) ) {
						continue;
					}

					$banning_type = check_banning_type( $user_id );
					if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
						continue;
					}

					if ( 'one_time' == $account_anniversary_type ) {
						self::award_one_time_account_anniversary_points( $user_id, $user );
					} else if ( 'every_year' == $account_anniversary_type ) {
						self::award_every_year_account_anniversary_points( $user_id, $user );
					}
				}
			} else {
				$rules = get_option( 'rs_account_anniversary_rules' );
				if ( ! srp_check_is_array( $rules ) ) {
					return;
				}

				if ( ! $user_id ) {
					$user_ids = self::get_matched_user_ids( 'account_anniv' );
					if ( ! srp_check_is_array( $user_ids ) ) {
						return;
					}
				} else {
					$user_ids = array( $user_id );
				}

				foreach ( $rules as $rule_key => $rule_value ) {

					$level_name  = isset( $rule_value[ 'level_name' ] ) ? $rule_value[ 'level_name' ] : '';
					$duration    = isset( $rule_value[ 'duration' ] ) ? $rule_value[ 'duration' ] : '';
					$point_value = isset( $rule_value[ 'point_value' ] ) ? $rule_value[ 'point_value' ] : '';
					if ( ! $level_name || ! $duration || ! $point_value ) {
						continue;
					}

					foreach ( $user_ids as $user_id ) {

						$user = get_user_by( 'ID', $user_id );
						if ( ! is_object( $user ) ) {
							continue;
						}

						if ( ! allow_reward_points_for_user( $user_id ) ) {
							continue;
						}

						$banning_type = check_banning_type( $user_id );
						if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
							continue;
						}

						if ( ! self::validate_rule_based_account_anniversary_rule( $user_id, $rule_key, $rule_value ) ) {
							continue;
						}

						$points_awarded = self::award_rule_based_account_anniversary_points( $user_id, $user, $rule_value );
						if ( 'yes' != $points_awarded ) {
							continue;
						}

						$stored_rule = get_user_meta( $user_id, 'rs_stored_account_anniversary_rule', true );
						if ( srp_check_is_array( $stored_rule ) ) {
							update_user_meta( $user_id, 'rs_stored_account_anniversary_rule', $stored_rule + array( $rule_key => $rule_value ) );
						} else {
							update_user_meta( $user_id, 'rs_stored_account_anniversary_rule', array( $rule_key => $rule_value ) );
						}
					}
				}
			}
		}

		/**
		 *  Validate rule based account anniversary rule.
		 */
		public static function validate_rule_based_account_anniversary_rule(
			$user_id,
			$settings_rule_key,
			$settings_rule_value 
		) {

			$stored_rule = get_user_meta( $user_id, 'rs_stored_account_anniversary_rule', true );
			if ( ! srp_check_is_array( $stored_rule ) ) {
				return true;
			}

			if ( ! isset( $stored_rule[ $settings_rule_key ] ) ) {
				return true;
			}

			$stored_duration    = isset( $stored_rule[ $settings_rule_key ][ 'duration' ] ) ? $stored_rule[ $settings_rule_key ][ 'duration' ] : '';
			$stored_point_value = isset( $stored_rule[ $settings_rule_key ][ 'point_value' ] ) ? $stored_rule[ $settings_rule_key ][ 'point_value' ] : '';
			if ( ! $stored_duration || ! $stored_point_value ) {
				return true;
			}

			$settings_duration    = isset( $settings_rule_value[ 'duration' ] ) ? $settings_rule_value[ 'duration' ] : '';
			$settings_point_value = isset( $settings_rule_value[ 'point_value' ] ) ? $settings_rule_value[ 'point_value' ] : '';
			if ( $settings_duration == $stored_duration && $stored_point_value == $settings_point_value ) {
				return false;
			}

			return true;
		}

		/**
		 *  Get matched user ids.
		 */
		public static function get_matched_user_ids( $type ) {

			global $wpdb;
			$db = &$wpdb;

			if ( 'account_anniv' == $type ) {
				$date     = gmdate( 'Y-m-d' );
				$user_ids = $db->get_col( "SELECT DISTINCT ID FROM {$db->users} as users WHERE DATE_FORMAT(users.user_registered,'%M-%d') = DATE_FORMAT('$date','%M-%d')" );
			} else if ( 'custom_single_anniv' == $type ) {
				$user_ids = $db->get_col( "SELECT DISTINCT ID FROM {$db->users} as u
                                                INNER JOIN {$db->usermeta} as um ON ( u.ID = um.user_id ) AND um.meta_key ='rs_single_anniversary_date'
                                                WHERE um.meta_key is not null" );
			} else if ( 'custom_multiple_anniv' == $type ) {
				$user_ids = $db->get_col( "SELECT DISTINCT ID FROM {$db->users} as u
                                                INNER JOIN {$db->usermeta} as um ON ( u.ID = um.user_id ) AND um.meta_key ='rs_multiple_anniversary_dates'
                                                WHERE um.meta_key is not null" );
			}

			return srp_check_is_array( $user_ids ) ? $user_ids : array();
		}

		/**
		 *  Award one time account anniversary points.
		 */
		public static function award_one_time_account_anniversary_points(
			$user_id,
			$user 
		) {

			if ( ! is_object( $user ) ) {
				return;
			}

			if ( get_user_meta( $user_id, 'rs_stored_account_anniversary_timestamp', true ) ) {
				return;
			}

			$user_registered_timestamp = strtotime( $user->user_registered );
			$user_registered_yr        = absint( gmdate( 'Y' ) - gmdate( 'Y', $user_registered_timestamp ) );
			if ( ! $user_registered_yr || gmdate( 'Y' ) <= gmdate( 'Y', $user_registered_timestamp ) ) {
				return;
			}

			$current_date_timestamp = strtotime( gmdate( 'Y-m-d' ) );
			$reached_timestamp      = strtotime( gmdate( 'Y' ) . gmdate( '-m-d', $user_registered_timestamp ) );
			if ( $current_date_timestamp != $reached_timestamp ) {
				return;
			}

			self::insert_anniversary_points( $user_id, 'AAP', get_option( 'rs_account_anniversary_point_value' ) );

			update_user_meta( $user_id, 'rs_stored_account_anniversary_timestamp', $reached_timestamp );
		}

		/**
		 *  Award every year account anniversary points.
		 */
		public static function award_every_year_account_anniversary_points(
			$user_id,
			$user 
		) {

			if ( ! is_object( $user ) ) {
				return;
			}

			$user_registered_timestamp = strtotime( $user->user_registered );
			$stored_date_timestamp     = get_user_meta( $user_id, 'rs_stored_account_anniversary_timestamp', true );
			$matched_timestamp         = ! empty( $stored_date_timestamp ) ? $stored_date_timestamp : $user_registered_timestamp;
			$user_registered_yr        = absint( gmdate( 'Y' ) - gmdate( 'Y', $matched_timestamp ) );
			if ( ! $user_registered_yr || gmdate( 'Y' ) <= gmdate( 'Y', $matched_timestamp ) ) {
				return;
			}

			$current_date_timestamp = strtotime( gmdate( 'Y-m-d' ) );
			$reached_timestamp      = strtotime( gmdate( 'Y' ) . gmdate( '-m-d', $matched_timestamp ) );
			if ( $current_date_timestamp != $reached_timestamp ) {
				return;
			}

			self::insert_anniversary_points( $user_id, 'AAP', get_option( 'rs_account_anniversary_point_value' ) );

			update_user_meta( $user_id, 'rs_stored_account_anniversary_timestamp', $reached_timestamp );
		}

		/**
		 *  Award rule based anniversary points.
		 */
		public static function award_rule_based_account_anniversary_points(
			$user_id,
			$user,
			$rule_value 
		) {

			if ( ! is_object( $user ) ) {
				return 'no';
			}

			$duration    = isset( $rule_value[ 'duration' ] ) ? $rule_value[ 'duration' ] : '';
			$point_value = isset( $rule_value[ 'point_value' ] ) ? $rule_value[ 'point_value' ] : '';
			if ( ! $duration || ! $point_value ) {
				return 'no';
			}

			$current_date_timestamp    = strtotime( gmdate( 'Y-m-d' ) );
			$user_registered_timestamp = strtotime( gmdate( 'Y-m-d', strtotime( $user->user_registered ) ) );
			$reached_timestamp         = strtotime( "+$duration year", $user_registered_timestamp );
			if ( $current_date_timestamp != $reached_timestamp ) {
				return 'no';
			}

			self::insert_anniversary_points( $user_id, 'AAP', $point_value );

			return 'yes';
		}

		/**
		 *  Insert anniversary points.
		 */
		public static function insert_anniversary_points(
			$user_id,
			$event_slug,
			$point_value 
		) {

			$new_obj = new RewardPointsOrder( 0, 'no' );
			if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				$new_obj->check_point_restriction( $point_value, 0, $event_slug, $user_id, '', '', '', '', '' );
			} else {
				$valuestoinsert = array(
					'pointstoinsert'    => $point_value,
					'event_slug'        => $event_slug, /* Anniversary Points */
					'user_id'           => $user_id,
					'totalearnedpoints' => $point_value,
				);

				$new_obj->total_points_management( $valuestoinsert, false );
			}
		}

		/**
		 *  Render custom anniversary field.
		 */
		public static function award_custom_anniversary_points() {

			if ( 'yes' != get_option( 'rs_anniversary_points_activated' ) || 'yes' != get_option( 'rs_enable_custom_anniversary_point' ) ) {
				return;
			}

			self::award_single_anniversary_points();

			self::award_multiple_anniversary_points();
		}

		/**
		 *  Award single anniversary points.
		 */
		public static function award_single_anniversary_points( $user_id = false ) {

			$custom_anniversary_type = get_option( 'rs_custom_anniversary_point_type', 'single_anniversary' );
			if ( 'single_anniversary' != $custom_anniversary_type ) {
				return;
			}

			$point_value = get_option( 'rs_custom_anniversary_point_value' );
			if ( ! $point_value ) {
				return;
			}

			if ( ! $user_id ) {
				$user_ids = self::get_matched_user_ids( 'custom_single_anniv' );
				if ( ! srp_check_is_array( $user_ids ) ) {
					return;
				}
			} else {
				$user_ids = array( $user_id );
			}

			$current_yr = gmdate( 'Y' );

			foreach ( $user_ids as $user_id ) {

				$user = get_user_by( 'ID', $user_id );
				if ( ! is_object( $user ) ) {
					continue;
				}

				if ( ! allow_reward_points_for_user( $user_id ) ) {
					continue;
				}

				$banning_type = check_banning_type( $user_id );
				if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
					continue;
				}

				$single_anniv_timestamp = strtotime( get_user_meta( $user_id, 'rs_single_anniversary_date', true ) );
				if ( ! $single_anniv_timestamp ) {
					continue;
				}

				$stored_anniv_year = get_user_meta( $user_id, 'rs_stored_single_anniversary_year', true );
				if ( 'yes' != get_option( 'rs_enable_repeat_custom_anniversary_point' ) && $stored_anniv_year ) {
					continue;
				}

				if ( $stored_anniv_year && $current_yr <= $stored_anniv_year ) {
					continue;
				}

				$current_date_timestamp = strtotime( gmdate( 'Y-m-d' ) );
				$reached_timestamp      = strtotime( $current_yr . gmdate( '-m-d', $single_anniv_timestamp ) );
				if ( $current_date_timestamp != $reached_timestamp ) {
					continue;
				}

				self::insert_anniversary_points( $user_id, 'CSAP', $point_value );

				update_user_meta( $user_id, 'rs_stored_single_anniversary_year', $current_yr );
			}
		}

		/**
		 *  Award multiple anniversary points.
		 */
		public static function award_multiple_anniversary_points( $user_id = false ) {

			$custom_anniversary_type = get_option( 'rs_custom_anniversary_point_type', 'single_anniversary' );
			if ( 'multiple_anniversary' != $custom_anniversary_type ) {
				return;
			}

			$rules = get_option( 'rs_custom_anniversary_rules' );
			if ( ! srp_check_is_array( $rules ) ) {
				return;
			}

			if ( ! $user_id ) {
				$user_ids = self::get_matched_user_ids( 'custom_multiple_anniv' );
				if ( ! srp_check_is_array( $user_ids ) ) {
					return;
				}
			} else {
				$user_ids = array( $user_id );
			}

			$current_yr = gmdate( 'Y' );
			foreach ( $rules as $rule_key => $rule_value ) {

				$field_name  = isset( $rule_value[ 'field_name' ] ) ? $rule_value[ 'field_name' ] : '';
				$point_value = isset( $rule_value[ 'point_value' ] ) ? $rule_value[ 'point_value' ] : '';
				$repeat      = isset( $rule_value[ 'repeat' ] ) ? $rule_value[ 'repeat' ] : '';
				if ( ! $field_name || ! $point_value ) {
					continue;
				}

				foreach ( $user_ids as $user_id ) {

					$user = get_user_by( 'ID', $user_id );
					if ( ! is_object( $user ) ) {
						continue;
					}

					if ( ! allow_reward_points_for_user( $user_id ) ) {
						continue;
					}

					$banning_type = check_banning_type( $user_id );
					if ( 'earningonly' == $banning_type || 'both' == $banning_type ) {
						continue;
					}

					$multiple_anniversary_dates = get_user_meta( $user_id, 'rs_multiple_anniversary_dates', true );
					if ( ! srp_check_is_array( $multiple_anniversary_dates ) ) {
						continue;
					}

					$anniversary_date = isset( $multiple_anniversary_dates[ $rule_key ] ) ? $multiple_anniversary_dates[ $rule_key ] : '';
					if ( ! $anniversary_date ) {
						continue;
					}

					$stored_anniv_year = get_user_meta( $user_id, 'rs_stored_multiple_anniversary_year_' . $rule_key, true );
					if ( ! self::validate_multiple_anniversary_rule( $user_id, $rule_key, $rule_value ) && 'on' != $repeat && $stored_anniv_year ) {
						continue;
					}

					if ( $stored_anniv_year && $current_yr <= $stored_anniv_year ) {
						continue;
					}

					$current_date_timestamp = strtotime( gmdate( 'Y-m-d' ) );
					$reached_timestamp      = strtotime( $current_yr . gmdate( '-m-d', strtotime( $anniversary_date ) ) );
					if ( $current_date_timestamp != $reached_timestamp ) {
						continue;
					}

					update_option( 'rs_multiple_anniversary_field_name', $field_name );

					self::insert_anniversary_points( $user_id, 'CMAP', $point_value );

					$stored_rule = get_user_meta( $user_id, 'rs_stored_multiple_anniversary_rules', true );
					if ( srp_check_is_array( $stored_rule ) ) {
						update_user_meta( $user_id, 'rs_stored_multiple_anniversary_rules', $stored_rule + array( $rule_key => $rule_value ) );
					} else {
						update_user_meta( $user_id, 'rs_stored_multiple_anniversary_rules', array( $rule_key => $rule_value ) );
					}

					update_user_meta( $user_id, 'rs_stored_multiple_anniversary_year_' . $rule_key, $current_yr );

					update_user_meta( $user_id, 'rs_stored_multiple_anniversary_point_' . $rule_key, $point_value );
				}
			}
		}

		/**
		 *  Validate multiple anniversary rule.
		 */
		public static function validate_multiple_anniversary_rule(
			$user_id,
			$settings_rule_key,
			$settings_rule_value 
		) {

			$stored_rule = get_user_meta( $user_id, 'rs_stored_multiple_anniversary_rules', true );
			if ( ! srp_check_is_array( $stored_rule ) ) {
				return true;
			}

			if ( ! isset( $stored_rule[ $settings_rule_key ] ) ) {
				return true;
			}

			$stored_field_name  = isset( $stored_rule[ $settings_rule_key ][ 'field_name' ] ) ? $stored_rule[ $settings_rule_key ][ 'field_name' ] : '';
			$stored_point_value = isset( $stored_rule[ $settings_rule_key ][ 'point_value' ] ) ? $stored_rule[ $settings_rule_key ][ 'point_value' ] : '';
			if ( ! $stored_field_name || ! $stored_point_value ) {
				return true;
			}

			$settings_field_name  = isset( $settings_rule_value[ 'field_name' ] ) ? $settings_rule_value[ 'field_name' ] : '';
			$settings_point_value = isset( $settings_rule_value[ 'point_value' ] ) ? $settings_rule_value[ 'point_value' ] : '';
			if ( $stored_field_name == $settings_field_name && $stored_point_value == $settings_point_value ) {
				return false;
			}

			return true;
		}

		/**
		 *  Render custom anniversary field.
		 */
		public static function render_custom_anniversary_fields() {

			if ( 'yes' != get_option( 'rs_anniversary_points_activated' ) || 'yes' != get_option( 'rs_enable_custom_anniversary_point' ) ) {
				return;
			}

			$custom_anniversary_type = get_option( 'rs_custom_anniversary_point_type', 'single_anniversary' );
			if ( 'single_anniversary' == $custom_anniversary_type ) {
				$points_value = get_option( 'rs_custom_anniversary_point_value' );
				if ( ! $points_value ) {
					return;
				}

				$template_args = array(
					'id'         => 'rs_single_anniversary_date',
					'value'      => get_user_meta( get_current_user_id(), 'rs_single_anniversary_date', true ),
					'field_name' => get_option( 'rs_custom_anniversary_field_name', 'Anniversary' ),
					'field_desc' => str_replace( '{anniversary_points}', '<b>' . $points_value . '</b>', get_option( 'rs_custom_anniversary_field_desc', 'Select the anniversary date to earn {anniversary_points} points when reaching the given date.' ) ),
					'classname'  => 'woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide',
				);

				srp_get_template( 'single-anniversary-date.php', $template_args );
			} else {
				$rules = get_option( 'rs_custom_anniversary_rules' );
				if ( ! srp_check_is_array( $rules ) ) {
					return;
				}

				foreach ( $rules as $rule_key => $rule_value ) {
					$field_name      = isset( $rule_value[ 'field_name' ] ) ? $rule_value[ 'field_name' ] : '';
					$point_value     = isset( $rule_value[ 'point_value' ] ) ? $rule_value[ 'point_value' ] : '';
					$field_desc      = isset( $rule_value[ 'desc' ] ) ? $rule_value[ 'desc' ] : '';
					$field_desc      = str_replace( array( '{anniversary_name}', '{anniversary_points}' ), array( '<b>' . $field_name . '</b>', '<b>' . $point_value . '</b>' ), $field_desc );
					$mandatory_field = isset( $rule_value[ 'mandatory' ] ) ? $rule_value[ 'mandatory' ] : '';
					if ( ! $field_name || ! $point_value || ! $field_desc ) {
						continue;
					}

					$multiple_anniversary_dates = get_user_meta( get_current_user_id(), 'rs_multiple_anniversary_dates', true );

					$template_args = array(
						'id'              => "rs_multiple_anniversary_dates[$rule_key]",
						'name'            => "rs_multiple_anniversary_dates[$rule_key]",
						'value'           => ! empty( $multiple_anniversary_dates[ $rule_key ] ) ? $multiple_anniversary_dates[ $rule_key ] : '',
						'field_name'      => $field_name,
						'field_desc'      => $field_desc,
						'mandatory_field' => $mandatory_field,
						'classname'       => 'woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide',
					);

					srp_get_template( 'multiple-anniversary-date.php', $template_args );
				}
			}
		}

		/**
		 *  Custom anniversary save.
		 */
		public static function custom_anniversary_save( $user_id ) {

			$user = get_user_by( 'ID', $user_id );
			if ( ! is_object( $user ) ) {
				return;
			}

			if ( isset( $_REQUEST[ 'rs_single_anniversary_date' ] ) && ! empty( wc_clean( wp_unslash( $_REQUEST[ 'rs_single_anniversary_date' ] ) ) ) ) {
				$single_anniversary_date = wc_clean( wp_unslash( $_REQUEST[ 'rs_single_anniversary_date' ] ) );
				update_user_meta( $user_id, 'rs_single_anniversary_date', $single_anniversary_date );
			}

			if ( isset( $_REQUEST[ 'rs_multiple_anniversary_dates' ] ) && ! empty( wc_clean( wp_unslash( $_REQUEST[ 'rs_multiple_anniversary_dates' ] ) ) ) ) {
				$multiple_anniversary_dates = wc_clean( wp_unslash( $_REQUEST[ 'rs_multiple_anniversary_dates' ] ) );
				update_user_meta( $user_id, 'rs_multiple_anniversary_dates', $multiple_anniversary_dates );
			}

			self::award_single_anniversary_points( $user_id );

			self::award_multiple_anniversary_points( $user_id );

			self::award_account_anniversary_points( $user_id );
		}

		/**
		 *  Add anniversary points on page load.
		 */
		public static function add_anniversary_points_on_page_load(
			$user_name,
			$user 
		) {

			if ( ! is_object( $user ) ) {
				return;
			}

			self::award_single_anniversary_points( $user->ID );

			self::award_multiple_anniversary_points( $user->ID );

			self::award_account_anniversary_points( $user->ID );
		}

		/**
		 *  Validate anniversary field.
		 */
		public static function validate_anniversary_field( $errors ) {

			if ( self::validate_single_anniversary_error() ) {

				$errors->add( 'error', str_replace( '{field_name}', get_option( 'rs_custom_anniversary_field_name', 'Anniversary' ), __( '{field_name} field is mandatory', 'rewardystem' ) ) );

				return $errors;
			}

			if ( self::validate_multiple_anniversary_error() ) {

				$multiple_anniversary_dates = isset( $_REQUEST[ 'rs_multiple_anniversary_dates' ] ) ? wc_clean( wp_unslash( $_REQUEST[ 'rs_multiple_anniversary_dates' ] ) ) : '';
				$rules                      = get_option( 'rs_custom_anniversary_rules' );
				if ( srp_check_is_array( $rules ) ) {
					foreach ( $rules as $rule_key => $rule_value ) {
						$field_name      = isset( $rule_value[ 'field_name' ] ) ? $rule_value[ 'field_name' ] : '';
						$point_value     = isset( $rule_value[ 'point_value' ] ) ? $rule_value[ 'point_value' ] : '';
						$field_desc      = isset( $rule_value[ 'desc' ] ) ? $rule_value[ 'desc' ] : '';
						$field_desc      = str_replace( array( '{anniversary_name}', '{anniversary_points}' ), array( '<b>' . $field_name . '</b>', '<b>' . $point_value . '</b>' ), $field_desc );
						$mandatory_field = isset( $rule_value[ 'mandatory' ] ) ? $rule_value[ 'mandatory' ] : '';
						if ( ! $field_name || ! $point_value || ! $field_desc || 'on' != $mandatory_field ) {
							continue;
						}

						if ( isset( $multiple_anniversary_dates[ $rule_key ] ) && $multiple_anniversary_dates[ $rule_key ] ) {
							continue;
						}

						$errors->add( 'error', str_replace( '{field_name}', $field_name, __( '{field_name} field is mandatory', 'rewardystem' ) ) );

						return $errors;
					}
				}
			}

			return $errors;
		}

		/**
		 *  Validate single anniversary error.
		 */
		public static function validate_single_anniversary_error() {

			$custom_anniversary_type = get_option( 'rs_custom_anniversary_point_type', 'single_anniversary' );
			if ( 'single_anniversary' != $custom_anniversary_type ) {
				return false;
			}

			$point_value = get_option( 'rs_custom_anniversary_point_value' );
			if ( ! $point_value ) {
				return false;
			}

			if ( ! isset( $_REQUEST[ 'rs_single_anniversary_date' ] ) ) {
				return false;
			}

			if ( 'yes' != get_option( 'rs_enable_mandatory_custom_anniversary_point' ) ) {
				return false;
			}

			if ( ! empty( wc_clean( wp_unslash( $_REQUEST[ 'rs_single_anniversary_date' ] ) ) ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Validate multiple anniversary error.
		 */
		public static function validate_multiple_anniversary_error() {

			$custom_anniversary_type = get_option( 'rs_custom_anniversary_point_type', 'single_anniversary' );
			if ( 'multiple_anniversary' != $custom_anniversary_type ) {
				return false;
			}

			if ( ! isset( $_REQUEST[ 'rs_multiple_anniversary_dates' ] ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Validate anniversary date field in edit account page.
		 */
		public static function validate_anniversary_field_edit_account( $errors ) {

			if ( self::validate_single_anniversary_error() ) {
				$errors->add( 'error', str_replace( '{field_name}', get_option( 'rs_custom_anniversary_field_name', 'Anniversary' ), __( '{field_name} field is mandatory', 'rewardystem' ) ) );
			}

			if ( self::validate_multiple_anniversary_error() ) {

				$multiple_anniversary_dates = isset( $_REQUEST[ 'rs_multiple_anniversary_dates' ] ) ? wc_clean( wp_unslash( $_REQUEST[ 'rs_multiple_anniversary_dates' ] ) ) : '';
				$rules                      = get_option( 'rs_custom_anniversary_rules' );
				if ( srp_check_is_array( $rules ) ) {
					foreach ( $rules as $rule_key => $rule_value ) {
						$field_name      = isset( $rule_value[ 'field_name' ] ) ? $rule_value[ 'field_name' ] : '';
						$point_value     = isset( $rule_value[ 'point_value' ] ) ? $rule_value[ 'point_value' ] : '';
						$field_desc      = isset( $rule_value[ 'desc' ] ) ? $rule_value[ 'desc' ] : '';
						$field_desc      = str_replace( array( '{anniversary_name}', '{anniversary_points}' ), array( '<b>' . $field_name . '</b>', '<b>' . $point_value . '</b>' ), $field_desc );
						$mandatory_field = isset( $rule_value[ 'mandatory' ] ) ? $rule_value[ 'mandatory' ] : '';
						if ( ! $field_name || ! $point_value || ! $field_desc || 'on' != $mandatory_field ) {
							continue;
						}

						if ( isset( $multiple_anniversary_dates[ $rule_key ] ) && $multiple_anniversary_dates[ $rule_key ] ) {
							continue;
						}

						$errors->add( 'error', str_replace( '{field_name}', $field_name, __( '{field_name} field is mandatory', 'rewardystem' ) ) );
						break;
					}
				}
			}
		}

		/**
		 * Render anniversary field in checkout page.
		 */
		public static function anniversary_field_in_checkout( $fields ) {

			if ( get_current_user_id() ) {
				return $fields;
			}

			if ( self::validate_single_anniversary_field_checkout_guest() ) {

				$fields[ 'account' ][ 'rs_single_anniversary_date' ] = array(
					'type'     => 'date',
					'label'    => get_option( 'rs_custom_anniversary_field_name', 'Anniversary' ),
					'required' => 'yes' == get_option( 'rs_enable_mandatory_custom_anniversary_point' ),
				);

				return $fields;
			}

			if ( self::validate_multiple_anniversary_field_checkout_guest() ) {

				$rules = get_option( 'rs_custom_anniversary_rules' );
				if ( srp_check_is_array( $rules ) ) {
					foreach ( $rules as $rule_key => $rule_value ) {
						$field_name      = isset( $rule_value[ 'field_name' ] ) ? $rule_value[ 'field_name' ] : '';
						$point_value     = isset( $rule_value[ 'point_value' ] ) ? $rule_value[ 'point_value' ] : '';
						$field_desc      = isset( $rule_value[ 'desc' ] ) ? $rule_value[ 'desc' ] : '';
						$field_desc      = str_replace( array( '{anniversary_name}', '{anniversary_points}' ), array( '<b>' . $field_name . '</b>', '<b>' . $point_value . '</b>' ), $field_desc );
						$mandatory_field = isset( $rule_value[ 'mandatory' ] ) ? $rule_value[ 'mandatory' ] : '';
						if ( ! $field_name || ! $point_value || ! $field_desc ) {
							continue;
						}

						$fields[ 'account' ][ 'rs_multiple_anniversary_dates_' . $rule_key ] = array(
							'type'     => 'date',
							'label'    => $field_name,
							'required' => 'on' == $mandatory_field,
						);
					}
				}

				return $fields;
			}

			return $fields;
		}

		/**
		 * Validate single anniversary field in checkout page for guest.
		 */
		public static function validate_single_anniversary_field_checkout_guest() {

			if ( 'yes' != get_option( 'rs_anniversary_points_activated' ) ) {
				return false;
			}

			$custom_anniversary_type = get_option( 'rs_custom_anniversary_point_type', 'single_anniversary' );
			if ( 'single_anniversary' != $custom_anniversary_type ) {
				return false;
			}

			$point_value = get_option( 'rs_custom_anniversary_point_value' );
			if ( ! $point_value ) {
				return false;
			}

			return true;
		}

		/**
		 * Validate multiple anniversary field in checkout page for guest.
		 */
		public static function validate_multiple_anniversary_field_checkout_guest() {

			if ( 'yes' != get_option( 'rs_anniversary_points_activated' ) ) {
				return false;
			}

			$custom_anniversary_type = get_option( 'rs_custom_anniversary_point_type', 'single_anniversary' );
			if ( 'multiple_anniversary' != $custom_anniversary_type ) {
				return false;
			}

			return true;
		}

		/**
		 * Update anniversary field in order.
		 */
		public static function update_anniversary_field_in_order( $order_id ) {

			$order = wc_get_order( $order_id );
			if ( ! is_object( $order ) ) {
				return;
			}

			$user_id = $order->get_user_id();
			if ( ! $user_id ) {
				return;
			}

			if ( isset( $_REQUEST[ 'rs_single_anniversary_date' ] ) && ! empty( wc_clean( wp_unslash( $_REQUEST[ 'rs_single_anniversary_date' ] ) ) ) ) {
				$single_anniversary_date = wc_clean( wp_unslash( $_REQUEST[ 'rs_single_anniversary_date' ] ) );
				update_user_meta( $user_id, 'rs_single_anniversary_date', $single_anniversary_date );
			}

			$rules = get_option( 'rs_custom_anniversary_rules' );
			if ( srp_check_is_array( $rules ) ) {
				$multiple_anniversary_dates = array();
				foreach ( $rules as $rule_key => $rule_value ) {
					$field_name  = isset( $rule_value[ 'field_name' ] ) ? $rule_value[ 'field_name' ] : '';
					$point_value = isset( $rule_value[ 'point_value' ] ) ? $rule_value[ 'point_value' ] : '';
					$field_desc  = isset( $rule_value[ 'desc' ] ) ? $rule_value[ 'desc' ] : '';
					$field_desc  = str_replace( array( '{anniversary_name}', '{anniversary_points}' ), array( '<b>' . $field_name . '</b>', '<b>' . $point_value . '</b>' ), $field_desc );
					if ( ! $field_name || ! $point_value || ! $field_desc ) {
						continue;
					}

					if ( isset( $_REQUEST[ 'rs_multiple_anniversary_dates_' . $rule_key ] ) && ! empty( wc_clean( wp_unslash( $_REQUEST[ 'rs_multiple_anniversary_dates_' . $rule_key ] ) ) ) ) {
						$multiple_anniversary_dates[ $rule_key ] = wc_clean( wp_unslash( $_REQUEST[ 'rs_multiple_anniversary_dates_' . $rule_key ] ) );
					}
				}

				if ( srp_check_is_array( $multiple_anniversary_dates ) ) {
					update_user_meta( $user_id, 'rs_multiple_anniversary_dates', $multiple_anniversary_dates );
				}
			}
		}

		/**
		 * May be add meta after points recorded.
		 */
		public static function may_be_add_meta_after_points_recorded(
			$user_id,
			$earned_points,
			$usedpoints,
			$earned_time,
			$table_args 
		) {

			if ( ! $user_id || ! isset( $table_args[ 'checkpoints' ] ) || ! $earned_points ) {
				return;
			}

			switch ( $table_args[ 'checkpoints' ] ) {
				case 'CSAP':
					$field_name = get_option( 'rs_custom_anniversary_field_name' , 'Anniversary');
					if ( $field_name ) {
						$stored_field_names = get_user_meta( $user_id, 'rs_stored_single_anniversary_field_names', true );
						if ( srp_check_is_array( $stored_field_names ) ) {
							update_user_meta( $user_id, 'rs_stored_single_anniversary_field_names', array( $earned_time => $field_name ) + $stored_field_names );
						} else {
							update_user_meta( $user_id, 'rs_stored_single_anniversary_field_names', array( $earned_time => $field_name ) );
						}
					}
					break;

				case 'CMAP':
					$field_name = get_option( 'rs_multiple_anniversary_field_name' );
					if ( $field_name ) {
						$stored_field_names = get_user_meta( $user_id, 'rs_stored_multiple_anniversary_field_names', true );
						if ( srp_check_is_array( $stored_field_names ) ) {
							update_user_meta( $user_id, 'rs_stored_multiple_anniversary_field_names', array( $earned_time => $field_name ) + $stored_field_names );
						} else {
							update_user_meta( $user_id, 'rs_stored_multiple_anniversary_field_names', array( $earned_time => $field_name ) );
						}
					}
					break;
			}
		}
	}

	Anniversary_Points_Handler::init();
}
