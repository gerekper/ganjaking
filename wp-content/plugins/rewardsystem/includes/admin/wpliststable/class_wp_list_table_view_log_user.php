<?php

// Integrate WP List Table for Users for Viewing Log

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class SRP_View_Log_Table extends WP_List_Table {

	// Prepare Items
	public function prepare_items() {
		$columns  = $this->get_columns();
		$sortable = $this->get_sortable_columns();

		$data   = $this->table_data();
		$user   = get_current_user_id();
		$screen = get_current_screen();
		if ( isset( $_REQUEST['s'] ) ) {
			$searchvalue = wc_clean( wp_unslash( $_REQUEST['s'] ) );
			$keyword     = "/$searchvalue/";

			$newdata = array();
			foreach ( $data as $eacharray => $value ) {
				$searchfunction = preg_grep( $keyword, $value );
				if ( ! empty( $searchfunction ) ) {
					$newdata[] = $data[ $eacharray ];
				}
			}
			usort( $newdata, array( &$this, 'sort_data' ) );
			foreach ( $data as $eacharray => $value ) {
				$newdata[ $eacharray ]['log_date'] = self::rs_display_date( $value );
			}

			$perPage     = RSTabManagement::rs_get_value_for_no_of_item_perpage( $user, $screen );
			$currentPage = $this->get_pagenum();
			$totalItems  = count( $newdata );

			$this->set_pagination_args(
				array(
					'total_items' => $totalItems,
					'per_page'    => $perPage,
				)
			);

			$newdata = array_slice( $newdata, ( ( $currentPage - 1 ) * $perPage ), $perPage );

			$this->_column_headers = array( $columns, array(), $sortable );

			$this->items = $newdata;
		} else {
			usort( $data, array( &$this, 'sort_data' ) );
			foreach ( $data as $eacharray => $value ) {
				$data[ $eacharray ]['log_date'] = self::rs_display_date( $value );
			}
			$perPage     = RSTabManagement::rs_get_value_for_no_of_item_perpage( $user, $screen );
			$currentPage = $this->get_pagenum();
			$totalItems  = count( $data );

			$this->set_pagination_args(
				array(
					'total_items' => $totalItems,
					'per_page'    => $perPage,
				)
			);

			$data = array_slice( $data, ( ( $currentPage - 1 ) * $perPage ), $perPage );

			$this->_column_headers = array( $columns, array(), $sortable );

			$this->items = $data;
		}
	}

	public function get_columns() {
		$columns = array(
			'sno'             => __( 'S.No', 'rewardsystem' ),
			'table_id'        => __( 'Table ID', 'rewardsystem' ),
			'user_name'       => __( 'Username', 'rewardsystem' ),
			'reward_for'      => __( 'Reward For', 'rewardsystem' ),
			'earned_points'   => __( 'Earned Points', 'rewardsystem' ),
			'redeemed_points' => __( 'Redeemed Points', 'rewardsystem' ),
			'total_points'    => __( 'Total Points', 'rewardsystem' ),
			'log_date'        => __( 'Date', 'rewardsystem' ),
			'expiry_date'     => __( 'Expiry Date', 'rewardsystem' ),
		);

		return $columns;
	}

	public function get_sortable_columns() {
		return array(
			'user_name'       => array( 'user_name', false ),
			'redeemed_points' => array( 'redeemed_points', false ),
			'earned_points'   => array( 'earned_points', false ),
			'log_date'        => array( 'log_date', false ),
			'total_points'    => array( 'total_points', false ),
			'expiry_date'     => array( 'expiry_date', false ),
		);
	}

	private function table_data() {
		global $wpdb, $woocommerce;
		$data               = array();
		$i                  = 1;
		$redeempoints       = '0';
		$totalpoints        = '0';
		$earnpoints         = '0';
		$user_ID            = isset( $_GET['view'] ) ? wc_clean( wp_unslash( $_GET['view'] ) ) : '';
		$getuserbyid        = get_user_by( 'id', $user_ID );
				$fetcharray = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}rsrecordpoints WHERE userid = %d AND showuserlog = false order by ID DESC ", $user_ID ), ARRAY_A );
		$fetcharray         = $fetcharray + (array) get_user_meta( $user_ID, '_my_points_log', true );
		if ( is_array( $fetcharray ) ) {
			foreach ( $fetcharray as $values ) {
								$user_id = isset( $values['userid'] ) ? @$values['userid'] : 0;
				$getuserbyid             = get_user_by( 'id', $user_id );
				if ( isset( $values['earnedpoints'] ) ) {
					if ( ! empty( $values['earnedpoints'] ) ) {
						if ( is_float( $values['earnedpoints'] ) ) {

							$total = round_off_type( number_format( $values['earnedpoints'], 2 ) );
						} else {
							$total = number_format( $values['earnedpoints'] );
						}
					} else {
						$total = isset( $values['earnedpoints'] ) && ! empty( $values['earnedpoints'] ) ? @$values['earnedpoints'] : 0;
					}
				}

				if ( '' != $values ) {
					if ( isset( $values['earnedpoints'] ) ) {
						$orderid = $values['orderid'];
						if ( (float) $woocommerce->version <= (float) ( '2.2.0' ) ) {
							$order = new WC_Order( $orderid );
						} else {
							$order = wc_get_order( $orderid );
						}
						$checkpoints          = $values['checkpoints'];
						$productid            = $values['productid'];
						$variationid          = $values['variationid'];
						$userid               = $values['userid'];
						$refuserid            = get_user_meta( $values['refuserid'], 'nickname', true );
						$reasonindetail       = $values['reasonindetail'];
						$redeempoints         = $values['redeempoints'];
						$masterlog            = true;
						$earnpoints           = $values['earnedpoints'];
						$user_deleted         = true;
						$order_status_changed = true;
						$csvmasterlog         = false;
						$totalpoints          = $values['totalpoints'];
						$nomineeid            = get_user_meta( $values['nomineeid'], 'nickname', true );
						$usernickname         = get_user_meta( $values['userid'], 'nickname', true );
						$nominatedpoints      = $values['nomineepoints'];
						$eventname            = RSPointExpiry::msg_for_log( $csvmasterlog, $user_deleted, $order_status_changed, $earnpoints, $checkpoints, $productid, $orderid, $variationid, $userid, $refuserid, $reasonindetail, $redeempoints, $masterlog, $nomineeid, $usernickname, $nominatedpoints, $values );
					} else {
						if ( ! empty( $values['points_earned_order'] ) ) {
							if ( '1' == get_option( 'rs_round_off_type' ) ) {
								$pointsearned = $values['points_earned_order'];
							} else {
								$pointsearned = number_format( $values['points_earned_order'] );
							}
						} else {
							$pointsearned = '0';
						}

						if ( ! empty( $values['before_order_points'] ) ) {
							if ( is_float( $values['before_order_points'] ) ) {
								$beforepoints = number_format( $values['before_order_points'], 2 );
							} else {
								$beforepoints = number_format( $values['before_order_points'] );
							}
						} else {
							$beforepoints = '0';
						}

						if ( ! empty( $values['points_redeemed'] ) ) {
							if ( '1' == get_option( 'rs_round_off_type' ) ) {
								$redeemedpoints = $values['points_redeemed'];
							} else {
								$redeemedpoints = number_format( (float) $values['points_redeemed'] );
							}
						} else {
							$redeemedpoints = '0';
						}
						if ( ! empty( $values['totalpoints'] ) ) {
							if ( '1' == get_option( 'rs_round_off_type' ) ) {
								$totalpoints = $values['totalpoints'];
							} else {
								$totalpoints = number_format( $values['totalpoints'] );
							}
						} else {
							$totalpoints = '0';
						}

						$usernickname = get_user_meta( $values['userid'], 'nickname', true );
						if ( ! empty( $values['reasonindetail'] ) ) {
							$rewarderfor = $values['reasonindetail'];
						} else {
							$rewarderfor = '';
						}

						$eventname      = $rewarderfor;
						$earnpoints     = $pointsearned;
						$redeemedpoints = $redeempoints;
					}

					$earnpoints   = round_off_type( $earnpoints );
					$redeempoints = 'yes' == get_option( 'rs_enable_round_off_type_for_calculation' ) ? $redeempoints : round_off_type( $redeempoints );

					$data[] = array(
						'sno'             => $i,
						'table_id'        => $values['id'],
						'user_name'       => $getuserbyid->user_login,
						'reward_for'      => $eventname,
						'earned_points'   => $earnpoints,
						'redeemed_points' => $redeempoints,
						'total_points'    => '' != $totalpoints ? round_off_type( $totalpoints ) : '0',
						'log_date'        => date_display_format( $values['earneddate'] ),
						'expiry_date'     => 999999999999 != $values['expirydate'] ? date_display_format( $values['expirydate'] ) : '-',
					);
					$i++;
				}
			}
		}
		return $data;
	}

	public function column_id( $item ) {
		return $item['sno'];
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'sno':
			case 'table_id':
			case 'user_name':
			case 'reward_for':
			case 'earned_points':
			case 'redeemed_points':
			case 'total_points':
			case 'log_date':
			case 'expiry_date':
				return $item[ $column_name ];

			default:
				return print_r( $item, true );
		}
	}

	public static function rs_display_date( $value ) {
		if ( '1' === get_option( 'rs_dispaly_time_format' ) ) {
			$dateformat        = 'd-m-Y h:i:s A';
			$value['log_date'] = is_numeric( $value['log_date'] ) ? date_i18n( $dateformat, $value['log_date'] ) : $value['log_date'];
			$value             = strftime( $value['log_date'] );
		} else {
			$timeformat        = get_option( 'time_format' );
			$dateformat        = get_option( 'date_format' ) . ' ' . $timeformat;
			$value['log_date'] = is_numeric( $value['log_date'] ) ? date_i18n( $dateformat, $value['log_date'] ) : $value['log_date'];
			$value             = strftime( $value['log_date'] );
		}
		return $value;
	}

	private function sort_data( $a, $b ) {

		$orderby = 'sno';
		$order   = 'asc';

		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = wc_clean( wp_unslash( $_GET['orderby'] ) );
		}

		if ( ! empty( $_GET['order'] ) ) {
			$order = wc_clean( wp_unslash( $_GET['order'] ) );
		}

		$result = strnatcmp( $a[ $orderby ], $b[ $orderby ] );

		if ( 'asc' == $order ) {
			return $result;
		}

		return -$result;
	}
}
