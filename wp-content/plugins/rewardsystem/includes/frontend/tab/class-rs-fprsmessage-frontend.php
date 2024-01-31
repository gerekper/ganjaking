<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}

if ( ! class_exists( 'RSFunctionForMessage' ) ) {

	/**
	 * Class RSFunctionForMessage
	 *
	 */
	class RSFunctionForMessage {

		/**
		 * Init function
		 *
		 */
		public static function init() {
			if ( '1' == get_option( 'rs_reward_table_position' ) ) {
				// Display my reward log after my account.
				add_action( 'woocommerce_after_my_account' , array( __CLASS__, 'reawrd_log_in_my_account_page' ) ) ;
			} else {
				// Display my reward log before my account.
				add_action( 'woocommerce_before_my_account' , array( __CLASS__, 'reawrd_log_in_my_account_page' ) ) ;
			}

			if ( '1' == get_option( 'rs_show_or_hide_date_filter' ) ) {
				// My reward table - date filter hook.
				add_filter( 'rs_my_reward_date_filter' , array( __CLASS__, 'my_reward_table_date_filter' ) ) ;
			}
		}

		/**
		 * My reward table - date filter
		 *
		 * @param $where query.
		 * @return string
		 */

		public static function my_reward_table_date_filter( $where ) {

			if ( isset( $_REQUEST[ 'rs_duration_type' ] , $_REQUEST[ 'rs_submit' ] ) ) {

				$to_date   = time() ;
				$durations = wc_clean( wp_unslash( $_REQUEST[ 'rs_duration_type' ] ) ) ;
				if ( '0' == $durations ) {
					return $where ;
				}

				switch ( $durations ) {

					case '1':
						$from_date = strtotime( '-1 month' ) ;
						$where     = "AND earneddate BETWEEN '$from_date' AND '$to_date'" ;
						break ;
					case '2':
						$from_date = strtotime( '-3 month' ) ;
						$where     = "AND earneddate BETWEEN '$from_date' AND '$to_date'" ;
						break ;
					case '3':
						$from_date = strtotime( '-6 month' ) ;
						$where     = "AND earneddate BETWEEN '$from_date' AND '$to_date'" ;
						break ;
					case '4':
						$from_date = strtotime( '-12 month' ) ;
						$where     = "AND earneddate BETWEEN '$from_date' AND '$to_date'" ;
						break ;
					case '5':
						$from_date = isset($_REQUEST[ 'rs_custom_from_date_field' ]) ? strtotime( wc_clean( wp_unslash( $_REQUEST[ 'rs_custom_from_date_field' ] ) ) ) :'';
						$to_date   = isset($_REQUEST[ 'rs_custom_to_date_field' ]) ? strtotime( wc_clean( wp_unslash( $_REQUEST[ 'rs_custom_to_date_field' ] ) ) ) :'';
						$where     = "AND earneddate BETWEEN '$from_date' AND '$to_date'" ;
						break ;
				}
			}

			return $where ;
		}

		/**
		 * Reward log in my account page.
		 *
		 * @return void
		 */
		public static function reawrd_log_in_my_account_page() {
			if ( 'yes' != get_option( 'rs_reward_content' ) ) {
				return ;
			}

			if ( 2 == get_option( 'rs_my_reward_table' ) ) {
				return ;
			}
						
			$BanType = check_banning_type( get_current_user_id() ) ;
			if ( 'earningonly' == $BanType || 'both' == $BanType ) {
				return ;
			}

			self::reward_log() ;
		}

		/**
		 * Display my reward table.
		 *
		 * @param $my_reward_menu_page display my reward menu page.
		 * @return void
		 */
		public static function reward_log( $my_reward_menu_page = false ) {

			$TableData = array(
				'points_log_sort'        => get_option( 'rs_points_log_sorting' ),
				'search_box'             => get_option( 'rs_show_hide_search_box_in_my_rewards_table' ),
				'page_size'              => get_option( 'rs_show_hide_page_size_my_rewards' ),
				'points_label_position'  => get_option( 'rs_reward_point_label_position' ),
				'total_points_label'     => get_option( 'rs_my_rewards_total' ),
				'display_currency_value' => get_option( 'rs_reward_currency_value' ),
				'sno'                    => get_option( 'rs_my_reward_points_s_no' ),
				'points_expiry'          => get_option( 'rs_my_reward_points_expire' ),
				'username'               => get_option( 'rs_my_reward_points_user_name_hide' ),
				'reward_for'             => get_option( 'rs_my_reward_points_reward_for_hide' ),
				'earned_points'          => get_option( 'rs_my_reward_points_earned_points_hide' ),
				'redeemed_points'        => get_option( 'rs_my_reward_points_redeemed_points_hide' ),
				'total_points'           => get_option( 'rs_my_reward_points_total_points_hide' ),
				'earned_date'            => get_option( 'rs_my_reward_points_earned_date_hide' ),
				'my_reward_label'        => get_option( 'rs_my_rewards_title' ),
				'label_sno'              => get_option( 'rs_my_rewards_sno_label' ),
				'label_username'         => get_option( 'rs_my_rewards_userid_label' ),
				'label_reward_for'       => get_option( 'rs_my_rewards_reward_for_label' ),
				'label_earned_points'    => get_option( 'rs_my_rewards_points_earned_label' ),
				'label_redeemed_points'  => get_option( 'rs_my_rewards_redeem_points_label' ),
				'label_total_points'     => get_option( 'rs_my_rewards_total_points_label' ),
				'label_earned_date'      => get_option( 'rs_my_rewards_date_label' ),
				'label_points_expiry'    => get_option( 'rs_my_rewards_points_expired_label' ),
				'per_page'               => ( '2' == get_option( 'rs_show_hide_page_size_my_rewards' , 1 ) ) ? get_option( 'rs_number_of_page_size_in_myaccount' , 5 ) : 5,
				'is_my_reward_menu_page' => $my_reward_menu_page,
				'pagination_limit'       => get_option( 'rs_numbers_to_display_pagination', '' ),
					) ;
			self::reward_log_table( $TableData ) ;
		}

		/**
		 * Get my reward table html.
		 *
		 * @param $TableData table args.
		 * @return string
		 */
		public static function reward_log_table( $TableData ) {
			ob_start() ;
			$UserId  = get_current_user_id() ;
			$BanType = check_banning_type( $UserId ) ;
			if ( 'redeemingonly' == $BanType || 'both' == $BanType ) {
				return ;
			}

			global $wpdb ;
						$db = &$wpdb;
						/**
						 * Hook:rs_my_reward_date_filter.
						 * 
						 * @since 1.0
						 */                        
			$where   = apply_filters( 'rs_my_reward_date_filter' , '' ) ;
			if ($where) {
				$UserLog = $db->get_results( $db->prepare( "SELECT * FROM {$db->prefix}rsrecordpoints WHERE userid = %d AND showuserlog = false $where" , $UserId ) , ARRAY_A ) ;
			} else {
				$UserLog = $db->get_results( $db->prepare( "SELECT * FROM {$db->prefix}rsrecordpoints WHERE userid = %d AND showuserlog = false" , $UserId ) , ARRAY_A ) ;
			}
			$UserLog = $UserLog + ( array ) get_user_meta( $UserId , '_my_points_log' , true ) ;
			if ( ! srp_check_is_array( $UserLog ) ) {
				return ;
			}

			$selected_duration_earned_point   = 0 ;
			$selected_duration_redeemed_point = 0 ;
			if ( isset( $_REQUEST[ 'rs_duration_type' ] ) ) {
				foreach ( $UserLog as $log ) {
					$selected_duration_earned_point   = isset( $log[ 'earnedpoints' ] ) ? $selected_duration_earned_point + $log[ 'earnedpoints' ] : 0 ;
					$selected_duration_redeemed_point = isset( $log[ 'redeempoints' ] ) ? $selected_duration_redeemed_point + $log[ 'redeempoints' ] : 0 ;
				}
			}

			echo wp_kses_post('<h2  class=my_rewards_title>' . $TableData[ 'my_reward_label' ] . '</h2>' );
			
			self::display_maximum_threshold_notice();
			
			$PointData       = new RS_Points_Data( $UserId ) ;
			$AvailablePoints = $PointData->total_available_points() ;
			$DisplayCurrency = $TableData[ 'display_currency_value' ] ;
			if ( '1' == $DisplayCurrency ) {
				$msg = '(' . $PointData->total_available_points_as_currency() . ')' ;
			} else {
				$msg = '' ;
			}
			if ( '1' == $TableData[ 'points_label_position' ] ) {
				echo wp_kses_post('<h4 class=my_reward_total> ' . $TableData[ 'total_points_label' ] . ' ' . round_off_type( $AvailablePoints ) . ' ' . $msg . '</h4>' );
			} else {
				echo wp_kses_post('<h4 class=my_reward_total> ' . round_off_type( $AvailablePoints ) . ' ' . $msg . $TableData[ 'total_points_label' ] . '</h4>' );
			}
						/**
						 * Hook:srp_above_reward_table.
						 * 
						 * @since 1.0
						 */
			$outputtablefields = apply_filters( 'srp_above_reward_table' , '' ) ;

			if ( '1' == get_option( 'rs_enable_footable_js' ) ) {
				$outputtablefields .= '<p> ' ;
				if ('1' ==  $TableData[ 'search_box' ] ) {
					?>
									<label><?php esc_html_e( 'Search:' , 'rewardsystem' ) ; ?></label> 
									<input id="filters" type="text"/>
									<?php
				}

				if ( '1' == $TableData[ 'page_size' ] ) {
					?>
									<label><?php esc_html_e( 'Page Size:' , 'rewardsystem' ) ; ?></label> 
									<select id="change-page-sizes">
										<option value="5">5</option>
										<option value="10">10</option>
										<option value="50">50</option>
										<option value="100">100</option>
									</select>  
										
									<?php
				}
				$outputtablefields .= '</p>' ;
				echo wp_kses_post(( '2' == $TableData[ 'search_box' ] && '2' == $TableData[ 'page_size' ] ) ? '' : $outputtablefields) ;
			}

			$DefaultColumn = array(
				'username',
				'reward_for',
				'earned_points',
				'redeemed_points',
				'points_expiry',
				'total_points',
				'earned_date',
					) ;

			$SortedColumn = srp_check_is_array( get_option( 'sorted_settings_list' ) ) ? get_option( 'sorted_settings_list' ) : $DefaultColumn ;
			$per_page     = isset( $TableData[ 'per_page' ] ) ? $TableData[ 'per_page' ] : '5' ;
			$pagination_limit = isset( $TableData[ 'pagination_limit' ] ) ? $TableData[ 'pagination_limit' ] : '0';

			// Pagination args when the footable JS dequeued.
			$pagination = self::get_pagination_args( $UserLog , $TableData , $per_page ) ;
			$UserLog    = srp_check_is_array( $pagination ) ? $pagination[ 'reward_log_data' ] : $UserLog ;
			$offset     = srp_check_is_array( $pagination ) ? $pagination[ 'offset' ] : 0 ;
			$page_count = srp_check_is_array( $pagination ) ? $pagination[ 'page_count' ] : 0 ;

			include SRP_PLUGIN_PATH . '/includes/frontend/views/class-rs-frontend-my-reward-table.php' ;
			$content = ob_get_contents() ;
			ob_end_flush() ;
			return $content ;
		}

		/**
		 * Get pagination arguments.
		 *
		 * @params $reward_log_data log args , $table_data table args , $per_page perpage.
		 * @return array
		 */
		public static function get_pagination_args( $reward_log_data, $table_data, $per_page ) {

			if ( '1' == get_option( 'rs_enable_footable_js' ) ) {
				return array() ;
			}

			if ( ! srp_check_is_array( $reward_log_data ) ) {
				return ;
			}

			$current_page    = isset( $_REQUEST[ 'page_no' ] ) ? wc_clean( wp_unslash( absint( $_REQUEST[ 'page_no' ] ) ) ) : '1' ;
			$offset          = ( $per_page * $current_page ) - $per_page ;
			$page_count      = ceil( count( $reward_log_data ) / $per_page ) ;
			$reward_log_data = array_slice( $reward_log_data , $offset , $per_page ) ;
			$menu_name       = '' != get_option( 'rs_my_reward_url_title' ) ? get_option( 'rs_my_reward_url_title' ) : 'sumo-rewardpoints' ;
			$query_arg       = isset( $table_data[ 'is_my_reward_menu_page' ] ) && true == $table_data[ 'is_my_reward_menu_page' ] ? $menu_name : '' ;

			return array(
				'reward_log_data' => $reward_log_data,
				'offset'          => $offset,
				'page_count'      => $page_count,
				'permalink'       => '' != $query_arg ? wc_get_endpoint_url( $query_arg ) : get_permalink(),
				'query_args'      => array(),
				'current_page'    => $current_page,
				'prev_page_count' => ( 0 == ( $current_page - 1 ) ) ? ( $current_page ) : ( $current_page - 1 ),
				'next_page_count' => ( ( $current_page + 1 ) <= ( $page_count ) ) ? ( $current_page + 1 ) : ( $current_page ),
					) ;
		}
		
		public static function display_maximum_threshold_notice() {
			
			if ( 'yes' != get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
				return ;
			}
			
			$max_threshold_points = get_option( 'rs_max_earning_points_for_user' ) ;
			if ( !$max_threshold_points ) {
				return ;
			}
			
			$pointsdata           = new RS_Points_Data( get_current_user_id() ) ;
			$available_points     = $pointsdata->total_available_points() ;
			
			if ( $max_threshold_points <= $available_points ) {
				$message = get_option('rs_maximum_threshold_error_message', 'Maximum Threshold Limit is <b>[threshold_value]</b>. Hence, you cannot earn points more than <b>[threshold_value]</b>');
				$message = str_replace( '[threshold_value]', $max_threshold_points , $message ) ;
				
				?>
					<div class="woocommerce-error rs-maximum-threshold-error"><?php echo wp_kses_post($message); ?></div>
				<?php
			}
		}
	}

	RSFunctionForMessage::init() ;
}
