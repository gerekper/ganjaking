<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ) ;
}

if ( ! class_exists( 'FPRewardSystemEncashTabList' ) ) {

	class FPRewardSystemEncashTabList extends WP_List_Table {

		public function __construct() {
			global $status , $page ;
			parent::__construct( array(
				'singular' => 'encashing_application' ,
				'plural'   => 'encashing_applications' ,
				'ajax'     => true
			) ) ;
		}

		public function column_default( $item, $column_name ) {
			return $item[ $column_name ] ;
		}

		public function column_userloginname( $item ) {
						
						$page = isset($_REQUEST[ 'page' ]) ?wc_clean(wp_unslash($_REQUEST[ 'page' ])):'' ;
						$tab = isset($_REQUEST[ 'tab' ]) ?wc_clean(wp_unslash($_REQUEST[ 'tab' ])):'' ;
						$section = isset($_REQUEST[ 'section' ]) ? wc_clean(wp_unslash($_REQUEST[ 'section' ])):'' ;  
						
			if ( 'Paid' == $item[ 'status' ]) {
				//Build row actions
				$actions = array(
					'cancel' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Cancel</a>' , sanitize_text_field($page) , $tab , $section , 'cancel' , $item[ 'id' ] ) ,
					'delete' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Delete</a>' , sanitize_text_field($page) , $tab , $section , 'encash_application_delete' , $item[ 'id' ] ) ,
						) ;

				//Return the title contents
				return sprintf( '%1$s %3$s' ,
						/* $1%s */ $item[ 'userloginname' ] ,
						/* $2%s */ $item[ 'id' ] ,
						/* $3%s */ $this->row_actions( $actions )
						) ;
			} elseif (  'Cancelled'  == $item[ 'status' ]) {
				//Build row actions
				$actions = array(
					'delete' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Delete</a>' , sanitize_text_field($page) , $tab , $section , 'encash_application_delete' , $item[ 'id' ] ) ,
						) ;

				//Return the title contents
				return sprintf( '%1$s %3$s' ,
						/* $1%s */ $item[ 'userloginname' ] ,
						/* $2%s */ $item[ 'id' ] ,
						/* $3%s */ $this->row_actions( $actions )
						) ;
			} else {
				//Build row actions
				$actions = array(
					'accept' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Accept</a>' , $page , $tab , $section , 'accept' , $item[ 'id' ] ) ,
					'cancel' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Cancel</a>' , $page , $tab, $section, 'cancel' , $item[ 'id' ] ) ,
					//'edit' => sprintf('<a href="?page=rewardsystem_callback&tab=rewardsystem_request_for_cash_back&encash_application_id=%s">Edit</a>', $item['id']),
					'delete' => sprintf( '<a href="?page=%s&tab=%s&section=%s&action=%s&id=%s">Delete</a>' , $page , $tab, $section, 'delete' , $item[ 'id' ] ) ,
						) ;

				//Return the title contents
				return sprintf( '%1$s %3$s' ,
						/* $1%s */ $item[ 'userloginname' ] ,
						/* $2%s */ $item[ 'id' ] ,
						/* $3%s */ $this->row_actions( $actions )
						) ;
			}
		}

		public function column_cb( $item ) {
			return sprintf(
					'<input type="checkbox" name="id[]" value="%s" />' , $item[ 'id' ]
					) ;
		}

		public function get_columns() {
			$columns = array(
				'cb'                   => '<input type="checkbox" />' , //Render a checkbox instead of text            
				'userloginname'        => __( 'Username' , 'rewardsystem' ) ,
				'pointstoencash'       => __( 'Points for Cashback' , 'rewardsystem' ) ,
				'pointsconvertedvalue' => __( 'Points equivalent in Amount ' . get_woocommerce_currency_symbol() , 'rewardsystem' ) ,
				'reasonforencash'      => __( 'Reason for Cashback' , 'rewardsystem' ) ,
				'paypalemailid'        => __( 'Paypal Address ' , 'rewardsystem' ) ,
				'otherpaymentdetails'  => __( 'Other Payment Details' , 'rewardsystem' ) ,
				'status'               => __( 'Application Status' , 'rewardsystem' ) ,
				'date'                 => __( 'Date' , 'rewardsystem' )
					) ;
			return $columns ;
		}

		public function get_sortable_columns() {
			$sortable_columns = array(
				'userloginname'        => array( 'userloginname' , false ) , //true means it's already sorted            
				'pointstoencash'       => array( 'pointstoencash' , false ) ,
				'pointsconvertedvalue' => array( 'pointsconvertedvalue' , false ) ,
				'reasonforencash'      => array( 'reasonforencash' , false ) ,
				'paypalemailid'        => array( 'paypalemailid' , false ) ,
				'otherpaymentdetails'  => array( 'otherpaymentdetails' , false ) ,
				'status'               => array( 'status' , false ) ,
				'date'                 => array( 'date' , false )
					) ;
			return $sortable_columns ;
		}

		public function get_bulk_actions() {
			$actions = array(
				'encash_application_delete' => __( 'Delete' , 'rewardsystem' ) ,
				'rspaid'                    => __( 'Mark as Approve' , 'rewardsystem' ) ,
				'rsdue'                     => __( 'Mark as Reject' , 'rewardsystem' ) ,
					) ;
			return $actions ;
		}

		public function process_bulk_action() {
			global $wpdb ;
			$db = &$wpdb;
			$table_name = $db->prefix . 'sumo_reward_encashing_submitted_data' ; // do not forget about tables prefix
			$ids        = isset( $_REQUEST[ 'id' ] ) ? absint($_REQUEST[ 'id' ]) : array() ;
			$ids        = srp_check_is_array( $ids ) ? $ids : ( empty( $_REQUEST[ 'id' ] ) ? array() : explode( ',' , $ids ) ) ;

			if ( ! srp_check_is_array( $ids ) ) {
				return ;
			}

			if ( 'encash_application_delete' === $this->current_action() ) {
				foreach ( $ids as $eachid ) {
					$user_ids = $db->get_results( $db->prepare( "SELECT * FROM {$db->prefix}sumo_reward_encashing_submitted_data WHERE id = %d", $eachid ) , ARRAY_A ) ;
					if ( ! srp_check_is_array( $user_ids ) ) {
						continue ;
					}

					foreach ( $user_ids as $value ) {
						$user_id     = $value[ 'userid' ] ;
						$updatoption = $value[ 'id' ] . 'cashbackreturn' ;
						if ( '1' == get_user_meta( $user_id , $updatoption , true ) ) {
							continue ;
						}

						$table_args   = array(
							'user_id'           => $user_id ,
							'pointstoinsert'    => $value[ 'pointstoencash' ] ,
							'checkpoints'       => 'RCBRP' ,
							'totalearnedpoints' => $value[ 'pointstoencash' ] ,
								) ;
						RSPointExpiry::insert_earning_points( $table_args ) ;
						RSPointExpiry::record_the_points( $table_args ) ;
						$wallet_label = '' != get_option( 'rs_encashing_wallet_menu_label' ) ? get_option( 'rs_encashing_wallet_menu_label' ) : 'Hoicker Wallet' ;
						if ( check_whether_hoicker_is_active() && $value[ 'otherpaymentdetails' ] == $wallet_label ) {
							$log_message = '' != get_option( 'hr_wallet_actions_rs_cashback_debited' ) ? get_option( 'hr_wallet_actions_rs_cashback_debited' ) : 'Cashback Debited' ;
							hr_wallet_remove_funds_function( $user_id , $value[ 'pointsconvertedvalue' ] , $log_message ) ;
							hr_wallet_mail_function( '' , $user_id , '' , 'usage_funds_user' , 'user' ) ;
							hr_wallet_mail_function( '' , $user_id , '' , 'usage_funds_admin' , 'admin' ) ;
						}
						update_user_meta( $user_id , $updatoption , '1' ) ;
					}
				}
				$idstodelete = implode( ',' , $ids ) ;
				$db->query( "DELETE FROM {$db->prefix}sumo_reward_encashing_submitted_data WHERE id IN($idstodelete)" ) ;
			} elseif ( 'rspaid' === $this->current_action() ) {
				$countids = count( $ids ) ;
				foreach ( $ids as $eachid ) {
					$db->update( "{$db->prefix}sumo_reward_encashing_submitted_data" , array( 'status' => 'Paid' ) , array( 'id' => $eachid ) ) ;
					$message = __( $countids . ' Status Changed to Paid' , 'rewardsystem' ) ;
				}
				if ( ! empty( $message ) ) :
					?>
					<div id="message" class="updated"><p><?php echo wp_kses_post($message); ?></p></div>
					<?php
				endif ;
			} elseif ( 'accept' === $this->current_action() ) {
				$countids = count( $ids ) ;
				foreach ( $ids as $eachid ) {
					$user_ids = $db->get_results( $db->prepare( "SELECT * FROM {$db->prefix}sumo_reward_encashing_submitted_data WHERE id = %d" , $eachid ) , ARRAY_A ) ;
					if ( ! srp_check_is_array( $user_ids ) ) {
						continue ;
					}

					foreach ( $user_ids as $value ) {
						$user_id     = $value[ 'userid' ] ;
						$updatoption = $value[ 'id' ] . 'walletia_cashback' ;
						if ( '1' == get_user_meta( $user_id , $updatoption , true ) ) {
							continue ;
						}

						$wallet_label = '' != get_option( 'rs_encashing_wallet_menu_label' ) ? get_option( 'rs_encashing_wallet_menu_label' ) : 'Hoicker Wallet' ;
						if ( check_whether_hoicker_is_active() && $value[ 'otherpaymentdetails' ] == $wallet_label ) {
							//Cashback on wallet       
							$log_message = '' != get_option( 'hr_wallet_actions_rs_cashback_credited' )  ? get_option( 'hr_wallet_actions_rs_cashback_credited' ) : 'Cashback Credited' ;
							hr_wallet_add_credit_updates( '' , $user_id , $value[ 'pointsconvertedvalue' ] , $log_message ) ;
							hr_wallet_mail_function( '' , $user_id , '' , 'add_funds_user' , 'user' ) ;
							hr_wallet_mail_function( '' , $user_id , '' , 'add_funds_admin' , 'admin' ) ;
							update_user_meta( $user_id , $updatoption , '1' ) ;
						}
					}
					$db->update( "{$db->prefix}sumo_reward_encashing_submitted_data" , array( 'status' => 'Paid' ) , array( 'id' => $eachid ) ) ;
					$message = __( $countids . ' Status Changed to Paid' , 'rewardsystem' ) ;
				}
				if ( ! empty( $message ) ) :
					?>
					<div id="message" class="updated"><p><?php echo wp_kses_post($message); ?></p></div>
					<?php
				endif ;
			} elseif ( 'cancel' === $this->current_action() ) {
				$countids = count( $ids ) ;
				foreach ( $ids as $eachid ) {
					$db->update( "{$db->prefix}sumo_reward_encashing_submitted_data" , array( 'status' => 'Cancelled' ) , array( 'id' => $eachid ) ) ;
					$message  = __( $countids . ' Status Changed to Cancelled' , 'rewardsystem' ) ;
					$user_ids = $db->get_results( $db->prepare( "SELECT * FROM {$db->prefix}sumo_reward_encashing_submitted_data WHERE id = %d" , $eachid ) , ARRAY_A ) ;
					if ( ! srp_check_is_array( $user_ids ) ) {
						continue ;
					}

					foreach ( $user_ids as $value ) {
						$user_id     = $value[ 'userid' ] ;
						$updatoption = $value[ 'id' ] . 'cashbackreturn' ;
						if ( '1'  == get_user_meta( $user_id , $updatoption , true )) {
							continue ;
						}

						$table_args   = array(
							'user_id'           => $user_id ,
							'pointstoinsert'    => $value[ 'pointstoencash' ] ,
							'checkpoints'       => 'RCBRP' ,
							'totalearnedpoints' => $value[ 'pointstoencash' ] ,
								) ;
						RSPointExpiry::insert_earning_points( $table_args ) ;
						RSPointExpiry::record_the_points( $table_args ) ;
						update_user_meta( $user_id , $updatoption , '1' ) ;
						$wallet_label = '' != get_option( 'rs_encashing_wallet_menu_label' ) ? get_option( 'rs_encashing_wallet_menu_label' ) : 'Hoicker Wallet' ;
						if ( check_whether_hoicker_is_active() && $value[ 'otherpaymentdetails' ] == $wallet_label ) {
							$log_message = '' != get_option( 'hr_wallet_actions_rs_cashback_debited' ) ? get_option( 'hr_wallet_actions_rs_cashback_debited' ) : 'Cashback Debited' ;
							hr_wallet_remove_funds_function( $user_id , $value[ 'pointsconvertedvalue' ] , $log_message ) ;
							hr_wallet_mail_function( '' , $user_id , '' , 'usage_funds_user' , 'user' ) ;
							hr_wallet_mail_function( '' , $user_id , '' , 'usage_funds_admin' , 'admin' ) ;
						}
					}
				}
				if ( ! empty( $message ) ) :
					?>
					<div id="message" class="updated"><p><?php echo wp_kses_post($message); ?></p></div>
					<?php
				endif ;
			} elseif ( 'delete' === $this->current_action() ) {
				foreach ( $ids as $eachid ) {
					$user_ids = $db->get_results( $db->prepare( "SELECT * FROM $table_name WHERE id = %d" , $eachid ) , ARRAY_A ) ;
					if ( ! srp_check_is_array( $user_ids ) ) {
						continue ;
					}

					foreach ( $user_ids as $value ) {
						$user_id     = $value[ 'userid' ] ;
						$updatoption = $value[ 'id' ] . 'cashbackreturn' ;
						if ( '1' == get_user_meta( $user_id , $updatoption , true ) ) {
							continue ;
						}

						$table_args = array(
							'user_id'           => $user_id ,
							'pointstoinsert'    => $value[ 'pointstoencash' ] ,
							'checkpoints'       => 'RCBRP' ,
							'totalearnedpoints' => $value[ 'pointstoencash' ] ,
								) ;
						RSPointExpiry::insert_earning_points( $table_args ) ;
						RSPointExpiry::record_the_points( $table_args ) ;
						update_user_meta( $user_id , $updatoption , '1' ) ;
					}
				}
				$idtodelete = implode( ',' , $ids ) ;
				$db->query( "DELETE FROM {$db->prefix}sumo_reward_encashing_submitted_data WHERE id IN($idtodelete)" ) ;
				if ( ! empty( $message ) ) :
					?>
					<div id="message" class="updated"><p><?php echo wp_kses_post($message); ?></p></div>
					<?php
				endif ;
			} else {
				$countids = count( $ids ) ;
				foreach ( $ids as $eachid ) {
					$db->update( "{$db->prefix}sumo_reward_encashing_submitted_data" , array( 'status' => 'Due' ) , array( 'id' => $eachid ) ) ;
					$message = __( $countids . ' Status Changed to Due' , 'rewardsystem' ) ;
				}
				if ( ! empty( $message ) ) :
					?>
					<div id="message" class="updated"><p><?php echo wp_kses_post($message); ?></p></div>
					<?php
				endif ;
			}
			$redirect = remove_query_arg( array( 'action' , 'id' ) , get_permalink() ) ;
			wp_safe_redirect( $redirect ) ;
		}

		public function extra_tablenav( $which ) {
			global $wpdb ;
			$mainlistarray                 = array() ;
			$mainlistarray_alldata         = array() ;
			$mainlistarray_paypal          = array() ;
			$mainlistarray_alldata_heading = '' ;
			if ( 'top' == $which) {
				?>
				<input type="submit" class="button-primary" name="fprs_encash_export_csv_paypal" id="fprs_encash_export_csv_paypal" value="<?php esc_html_e( 'Export Due Points as CSV for Paypal Mass Payment' , 'rewardsystem' ) ; ?>"/>
				<input type="submit" class="button-primary" name="fprs_encash_export_csv_alldata" id="fprs_encash_export_csv_alldata" value="<?php esc_html_e( 'Export All Cashback Requests' , 'rewardsystem' ) ; ?>"/>
				<?php
								$getallresults = $wpdb->get_results($wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sumo_reward_encashing_submitted_data WHERE status=%s", 'Due') , ARRAY_A ) ;
				if ( isset( $getallresults ) ) {
					foreach ( $getallresults as $value ) {
						if ( '' != $value[ 'pointstoencash' ] && '' != $value[ 'paypalemailid' ] ) {
							$mainlistarray_paypal[] = array( $value[ 'paypalemailid' ] , $value[ 'pointsconvertedvalue' ] , get_woocommerce_currency() , $value[ 'userid' ] , get_option( 'rs_encashing_paypal_custom_notes' ) ) ;
						}
					}
					if ( isset( $_REQUEST[ 'fprs_encash_export_csv_paypal' ] ) ) {
						if ( is_array( $mainlistarray_paypal ) && ( ! empty( $mainlistarray_paypal ) ) ) {
							$dateformat = get_option( 'date_format' ) ;
							$name       = date_i18n( 'Y-m-d' ) ;
							ob_end_clean() ;
							header( 'Content-type: text/csv' ) ;
							header( 'Content-Disposition: attachment; filename=sumoreward_cashback_paypal' . $name . '.csv' ) ;
							header( 'Pragma: no-cache' ) ;
							header( 'Expires: 0' ) ;
							$output     = fopen( 'php://output' , 'w' ) ;
							foreach ( $mainlistarray_paypal as $row ) {
								if ( false != $row ) {
									fputcsv( $output , $row ) ; // here you can change delimiter/enclosure
								}
							}
							fclose( $output ) ;
							exit() ;
						}
					}
				}

				if ( isset( $getallresults ) ) {
					foreach ( $getallresults as $allvalue ) {
						if ( '' != $allvalue[ 'pointstoencash' ] ) {
							$mainlistarray_alldata_heading = 'Username,UserCurrentPoints,PointsforCashback,CurrencyCode,AmountforCashback,ReasonforEncashing,PaypalAddress,OtherPaymentDetails,ApplicationStatus,CashbackRequestedDate' . "\n" ;
							$mainlistarray_alldata[]       = array( $allvalue[ 'userloginname' ] , $allvalue[ 'encashercurrentpoints' ] , $allvalue[ 'pointstoencash' ] , get_woocommerce_currency() , $allvalue[ 'pointsconvertedvalue' ] , $allvalue[ 'reasonforencash' ] , $allvalue[ 'paypalemailid' ] , $allvalue[ 'otherpaymentdetails' ] , $allvalue[ 'status' ] , $allvalue[ 'date' ] ) ;
						}
					}
					if ( isset( $_REQUEST[ 'fprs_encash_export_csv_alldata' ] ) ) {
						$dateformat = get_option( 'date_format' ) ;
						$name       = date_i18n( 'Y-m-d' ) ;
						ob_end_clean() ;
						echo wp_kses_post($mainlistarray_alldata_heading );
						header( 'Content-type: text/csv' ) ;
						header( 'Content-Disposition: attachment; filename=sumoreward_cashback_alldata' . $name . '.csv' ) ;
						header( 'Pragma: no-cache' ) ;
						header( 'Expires: 0' ) ;
						$output     = fopen( 'php://output' , 'w' ) ;
						if ( is_array( $mainlistarray_alldata ) && ( ! empty( $mainlistarray_alldata ) ) ) {
							foreach ( $mainlistarray_alldata as $row ) {
								if ( false != $row ) {
									fputcsv( $output , $row ) ; // here you can change delimiter/enclosure
								}
							}
						}
						fclose( $output ) ;
						exit() ;
					}
				}
			}
		}

		public function prepare_items() {
			global $wpdb ;

			$per_page = 10 ; // constant, how much records will be shown per page

			$columns  = $this->get_columns() ;
			$hidden   = array() ;
			$sortable = $this->get_sortable_columns() ;

			// here we configure table headers, defined in our methods
			$this->_column_headers = array( $columns , $hidden , $sortable ) ;

			// [OPTIONAL] process bulk action if any
			$this->process_bulk_action() ;

			// will be used in pagination settings
			$total_items = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}sumo_reward_encashing_submitted_data" ) ;

			// prepare query params, as usual current page, order by and order direction
			$paged   = isset( $_REQUEST[ 'paged' ] ) ? max( 0 , intval( $_REQUEST[ 'paged' ] ) - 1 ) : 0 ;
			$paged   = $paged * $per_page + 1;
			$orderby = ( isset( $_REQUEST[ 'orderby' ] ) && in_array( sanitize_text_field($_REQUEST[ 'orderby' ]) , array_keys( $this->get_sortable_columns() ) ) ) ? sanitize_text_field($_REQUEST[ 'orderby' ] ): 'id' ;
			$order   = ( isset( $_REQUEST[ 'order' ] ) && in_array( sanitize_text_field($_REQUEST[ 'order' ]) , array( 'asc' , 'desc' ) ) ) ? sanitize_text_field($_REQUEST[ 'order' ]) : 'asc' ;

			// [REQUIRED] define $items array
			// notice that last argument is ARRAY_A, so we will retrieve array
			$this->items = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sumo_reward_encashing_submitted_data ORDER BY %s %s LIMIT %d OFFSET %d" , $orderby, $order, $per_page , $paged ) , ARRAY_A ) ;

			// [REQUIRED] configure pagination
			$this->set_pagination_args( array(
				'total_items' => $total_items , // total items defined above
				'per_page'    => $per_page , // per page constant defined at top of method
				'total_pages' => ceil( $total_items / $per_page ) // calculate pages count
			) ) ;
		}

	}

}
