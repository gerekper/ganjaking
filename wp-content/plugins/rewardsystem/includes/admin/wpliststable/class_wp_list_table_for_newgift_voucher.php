<?php

// Integrate WP List Table for Master Log

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php'  ;
}

class SRP_NewGiftVoucher_List_Table extends WP_List_Table {

	// Prepare Items
	public function prepare_items() {
		global $wpdb ;
		$this->process_bulk_action() ;
		$columns     = $this->get_columns() ;
		$hidden      = $this->get_hidden_columns() ;
		$sortable    = $this->get_sortable_columns() ;
		$user        = get_current_user_id() ;
		$screen      = get_current_screen() ;
		$perPage     = RSTabManagement::rs_get_value_for_no_of_item_perpage( $user , $screen ) ;
		$currentPage = $this->get_pagenum() ;
				$newdata     = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rsgiftvoucher" , ARRAY_A ) ;
		$num_rows    = count( $newdata ) ;
		$data        = $this->table_data() ;
		if ( isset( $_REQUEST[ 's' ] ) && '' != wc_clean(wp_unslash($_REQUEST[ 's' ])) ) {
			$searchvalue           = wc_clean(wp_unslash($_REQUEST[ 's' ] ));
			$keyword               = "$searchvalue" ;
			$data                  = $this->get_data_of_searched_code( $keyword , $newdata ) ;
			usort( $data , array( &$this, 'sort_data' ) ) ;
			$currentPage           = $this->get_pagenum() ;
			$totalItems            = count( $data ) ;
			$newdata               = array_slice( $data , ( ( $currentPage - 1 ) * $perPage ) , $perPage ) ;
			$this->_column_headers = array( $columns, $hidden, $sortable ) ;
			$this->items           = $newdata ;
		} else {
			usort( $data , array( &$this, 'sort_data' ) ) ;
			$currentPage           = $this->get_pagenum() ;
			$totalItems            = count( $data ) ;
			$this->set_pagination_args( array(
				'total_items' => $num_rows,
				'per_page'    => $perPage,
			) ) ;
			$data                  = array_slice( $data , ( ( $currentPage - 1 ) * $perPage ) , $perPage ) ;
			$this->_column_headers = array( $columns, $hidden, $sortable ) ;
			$this->items           = $data ;
		}
	}

	public function get_columns() {
		$columns = array(
			'cb'                      => '<input type="checkbox" />',
			'sno'                     => __( 'S.No' , 'rewardsystem' ),
			'reward_code'             => __( 'Voucher Code' , 'rewardsystem' ),
			'points_assigned'         => __( 'Points' , 'rewardsystem' ),
			'rewardcode_created_date' => __( 'Created on' , 'rewardsystem' ),
			'rewardcode_expired_date' => __( 'Expires on' , 'rewardsystem' ),
			'rewardcode_used_by'      => __( 'Voucher Code used by' , 'rewardsystem' ),
		) ;

		return $columns ;
	}

	public function get_hidden_columns() {
		return array() ;
	}

	public function column_cb( $item ) {
		return sprintf(
				'<input type="checkbox" name="id[]" value="%s" />' , $item[ 'cb' ]
		) ;
	}

	public function column_reward_code( $item ) {
				$page = isset($_REQUEST[ 'page' ]) ? wc_clean(wp_unslash($_REQUEST[ 'page' ])) : '';
				$tab = isset($_REQUEST[ 'tab' ]) ? wc_clean(wp_unslash($_REQUEST[ 'tab' ] )) : '';
				$section = isset($_REQUEST[ 'section' ]) ? wc_clean(wp_unslash($_REQUEST[ 'section' ] )) : '';
		//Build row actions
		$actions = array(
			'delete' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Delete</a>' , $page, $tab, $section, 'delete_row' , $item[ 'cb' ] ),
		) ;
		//Return the title contents
		return sprintf( '%1$s %3$s' ,
				/* $1%s */ $item[ 'reward_code' ] ,
				/* $2%s */ $item[ 'cb' ] ,
				/* $3%s */ $this->row_actions( $actions )
		) ;
	}

	public function get_bulk_actions() {
		$columns = array(
			'delete'     => __( 'Delete' , 'rewardsystem' ),
			'delete_all' => __( 'Delete All' , 'rewardsystem' ),
		) ;
		return $columns ;
	}

	public function get_sortable_columns() {
		return array(
			'points_assigned'         => array( 'points_assigned', false ),
			'sno'                     => array( 'sno', false ),
			'rewardcode_created_date' => array( 'rewardcode_created_date', false ),
			'rewardcode_expired_date' => array( 'rewardcode_expired_date', false ),
		) ;
	}

	public function process_bulk_action() {
		global $wpdb ;
		if ( 'delete_all' === $this->current_action() ) {
			$wpdb->query( "DELETE FROM {$wpdb->prefix}rsgiftvoucher") ;
		} elseif ( 'delete' === $this->current_action() ) {
			$newupdates = array() ;
			$ids        = isset( $_REQUEST[ 'id' ] ) ? absint($_REQUEST[ 'id' ] ): array() ;
			if ( is_array( $ids ) && ! empty( $ids ) ) {
				foreach ( $ids as $each_code ) {
					$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}rsgiftvoucher WHERE vouchercode=%s", $each_code) ) ;
				}
			}
		} elseif ( 'delete_row' === $this->current_action() ) {
			$ids = isset( $_REQUEST[ 'id' ] ) ? absint($_REQUEST[ 'id' ]) : '' ;
			$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}rsgiftvoucher WHERE vouchercode=%s", $ids) ) ;
		}
	}

	public function get_data_of_searched_code( $keyword, $subdatas ) {
		global $wpdb ;
		$subdatas   = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rsgiftvoucher" , ARRAY_A ) ;
		$i          = 1 ;
		$data       = array() ;
		if ( is_array( $subdatas ) && ! empty( $subdatas ) ) {
			foreach ( $subdatas as $value ) {
				if ( $value[ 'vouchercode' ] === $keyword ) {
					$user_ids         = '' != $value[ 'memberused' ] ? unserialize( $value[ 'memberused' ] ) : array() ;
					$voucher_code_for = $value[ 'voucher_code_usage' ] ;
					if ( '' != $voucher_code_for ) {
						if (  '1'  == $voucher_code_for) {
							if ( '' != $value[ 'memberused' ] ) {
								if ( is_array( $user_ids ) && ! empty( $user_ids ) ) {
									foreach ( $user_ids as $user_id ) {
										$userinfo                        = get_userdata( $user_id ) ;
										$usernames                       = $userinfo->user_login ;
										$username[ $value[ 'vouchercode' ] ] = $usernames ;
									}
								} else {
									$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
								}
							} else {
								$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
							}
						} elseif ( '' != $value[ 'memberused' ] ) {
							if ( is_array( $user_ids ) && ! empty( $user_ids ) ) {
								foreach ( $user_ids as $user_id ) {
									$userinfo                        = get_userdata( $user_id ) ;
									$usernames                       = $userinfo->user_login ;
									$username[ $value[ 'vouchercode' ] ] = $usernames ;
								}
							} else {
								$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
							}
						} else {
							$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
						}
					} elseif ( '' != $value[ 'memberused' ]) {
						if ( is_array( $user_ids ) && ! empty( $user_ids ) ) {
							foreach ( $user_ids as $user_id ) {
								$userinfo                        = get_userdata( $user_id ) ;
								$usernames                       = $userinfo->user_login ;
								$username[ $value[ 'vouchercode' ] ] = $usernames ;
							}
						} else {
							$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
						}
					} else {
						$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
					}
					$count  = count( $user_ids ) > 1 ? ( count( $user_ids ) - 1 ) . '+' : '1+' ;
					$data[] = array(
						'cb'                      => $value[ 'vouchercode' ],
						'sno'                     => $i,
						'reward_code'             => $value[ 'vouchercode' ],
						'points_assigned'         => $value[ 'points' ],
						'rewardcode_created_date' => $value[ 'vouchercreated' ],
						'rewardcode_expired_date' => '' != $value[ 'voucherexpiry' ] ? $value[ 'voucherexpiry' ] : 'Never',
						'rewardcode_used_by'      => count( $user_ids ) > 1 ? ( $username[ $value[ 'vouchercode' ] ] . ',<a href=' . add_query_arg( 'vouchercode' , $value[ 'vouchercode' ] , add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpgiftvoucher' ), SRP_ADMIN_URL ) ) . '>' . $count . '</a>' ) : $username[ $value[ 'vouchercode' ] ],
					) ;
					$i++ ;
				}
			}
		}
		return $data ;
	}

	private function table_data() {
		$data       = array() ;
		$i          = 1 ;
		global $wpdb ;
		$subdatas   = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rsgiftvoucher" , ARRAY_A ) ;
		if ( is_array( $subdatas ) && ! empty( $subdatas ) ) {
			foreach ( $subdatas as $value ) {
				if ( isset( $value[ 'vouchercode' ] ) ) {
					if ( '' != $value[ 'vouchercode' ] ) {
						if ( 0 != $i % 2 ) {
							$name = 'alternate' ;
						} else {
							$name = '' ;
						}
						$user_ids         = '' != $value[ 'memberused' ] ? unserialize( $value[ 'memberused' ] ) : array() ;
						$voucher_code_for = $value[ 'voucher_code_usage' ] ;
						if ( '' != $voucher_code_for ) {
							if ( '1'== $voucher_code_for ) {
								if ('' != $value[ 'memberused' ] ) {
									if ( is_array( $user_ids ) && ! empty( $user_ids ) ) {
										foreach ( $user_ids as $user_id ) {
											$userinfo                        = get_userdata( $user_id ) ;
											$usernames                       = $userinfo->user_login ;
											$username[ $value[ 'vouchercode' ] ] = $usernames ;
										}
									} else {
										$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
									}
								} else {
									$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
								}
							} elseif ( '' != $value[ 'memberused' ]  ) {
								if ( is_array( $user_ids ) && ! empty( $user_ids ) ) {
									foreach ( $user_ids as $user_id ) {
										$userinfo                        = get_userdata( $user_id ) ;
										$usernames                       = $userinfo->user_login ;
										$username[ $value[ 'vouchercode' ] ] = $usernames ;
									}
								} else {
									$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
								}
							} else {
								$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
							}
						} elseif ( ''!= $value[ 'memberused' ] ) {
							if ( is_array( $user_ids ) && ! empty( $user_ids ) ) {
								foreach ( $user_ids as $user_id ) {
									$userinfo                        = get_userdata( $user_id ) ;
									$usernames                       = $userinfo->user_login ;
									$username[ $value[ 'vouchercode' ] ] = $usernames ;
								}
							} else {
								$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
							}
						} else {
							$username[ $value[ 'vouchercode' ] ] = 'Not Yet' ;
						}
						$count  = count( $user_ids ) > 1 ? ( count( $user_ids ) - 1 ) . '+' : '1+' ;
						$data[] = array(
							'cb'                      => $value[ 'vouchercode' ],
							'sno'                     => $i,
							'reward_code'             => $value[ 'vouchercode' ],
							'points_assigned'         => $value[ 'points' ],
							'rewardcode_created_date' => $value[ 'vouchercreated' ],
							'rewardcode_expired_date' => '' != $value[ 'voucherexpiry' ] ? $value[ 'voucherexpiry' ] : 'Never',
							'rewardcode_used_by'      => count( $user_ids ) > 1 ? ( $username[ $value[ 'vouchercode' ] ] . ',<a href=' . add_query_arg( 'vouchercode' , $value[ 'vouchercode' ] , add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpgiftvoucher' ), SRP_ADMIN_URL ) ) . '>' . $count . '</a>' ) : $username[ $value[ 'vouchercode' ] ],
						) ;
						$i++ ;
					}
				}
			}
		}
		return $data ;
	}

	public function column_id( $item ) {
		return $item[ 'sno' ] ;
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'sno':
			case 'reward_code':
			case 'points_assigned':
			case 'rewardcode_created_date':
			case 'rewardcode_expired_date':
			case 'rewardcode_used_by':
				return $item[ $column_name ] ;
			default:
				return print_r( $item , true ) ;
		}
	}

	private function sort_data( $a, $b ) {

		$orderby = 'sno' ;
		$order   = 'asc' ;

		if ( ! empty( $_GET[ 'orderby' ] ) ) {
			$orderby = wc_clean(wp_unslash($_GET[ 'orderby' ])) ;
		}

		if ( ! empty( $_GET[ 'order' ] ) ) {
			$order = wc_clean(wp_unslash($_GET[ 'order' ])) ;
		}
		$result = strnatcmp( $a[ $orderby ] , $b[ $orderby ] ) ;

		if ( 'asc' == $order  ) {
			return $result ;
		}

		return -$result ;
	}
}
