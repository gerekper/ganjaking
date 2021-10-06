<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSCashBackFrontend' ) ) {

	class RSCashBackFrontend {

		public static function init() {
			add_action( 'woocommerce_after_my_account' , array( __CLASS__ , 'cash_back_log' ) ) ;
		}

		public static function cash_back_log() {
			if ( '2' == get_option( 'rs_my_cashback_table' ) ) {
				return ;
			}

			if ( 'yes' != get_option( 'rs_reward_content' ) ) {
				return ;
			}

			$TableData = array(
				'title'    => get_option( 'rs_my_cashback_title' ) ,
				'sno'      => get_option( 'rs_my_cashback_sno_label' ) ,
				'username' => get_option( 'rs_my_cashback_userid_label' ) ,
				'request'  => get_option( 'rs_my_cashback_requested_label' ) ,
				'status'   => get_option( 'rs_my_cashback_status_label' ) ,
				'action'   => get_option( 'rs_my_cashback_action_label' )
					) ;
			self::cash_back_log_table( $TableData ) ;
		}

		public static function cash_back_log_table( $TableData ) {
			$UserId  = get_current_user_id() ;
			$BanType = check_banning_type( $UserId ) ;
			if ( 'redeemingonly' == $BanType || 'both' == $BanType ) {
				return ;
			}
			
			global $wpdb ;
			$CashbackTableData = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sumo_reward_encashing_submitted_data WHERE userid = %d" , $UserId ) , ARRAY_A ) ;
			if ( ! srp_check_is_array( $CashbackTableData ) ) {
				return ;
			}

			ob_start() ;
			echo wp_kses_post('<h2 class=rs_my_cashback_title>' . $TableData[ 'title' ] . '</h2>' );
			?>
			<table class = "examples demo shop_table my_account_orders table-bordered" data-filter = "#filters" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">
				<thead>
					<tr>
						<th data-toggle="true" data-sort-initial = "true"><?php echo esc_html($TableData[ 'sno' ]) ; ?></th>
						<th><?php echo wp_kses_post($TableData[ 'username' ]) ; ?></th>
						<th><?php echo esc_html($TableData[ 'request' ]) ; ?></th>
						<th><?php echo esc_html($TableData[ 'status' ]) ; ?></th>
						<th><?php echo esc_html($TableData[ 'action' ]) ; ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( get_option( 'rs_points_log_sorting' ) == '1' ) {
						krsort( $CashbackTableData , SORT_NUMERIC ) ;
					}

					$i = 1 ;
					foreach ( $CashbackTableData as $Data ) {

						if ( ! srp_check_is_array( $Data ) ) {
							continue ;
						}

						$NickName        = get_user_meta( $Data[ 'userid' ] , 'nickname' , true ) ;
						$Log             = get_option( '_rs_localize_points_to_cash_log_in_my_cashback_table' ) ;
						$PointReplaceLog = str_replace( '[pointstocashback]' , $Data[ 'pointstoencash' ] , $Log ) ;
						$AmntReplacedLog = str_replace( '[cashbackamount]' , get_woocommerce_currency_symbol() . $Data[ 'pointsconvertedvalue' ] , $PointReplaceLog ) ;
						$status          = $Data[ 'status' ] ;
						$StatusToDisplay = array(
							'Cancelled' => __( 'Cancelled' , 'rewardsystem' ) ,
							'Paid'      => __( 'Paid' , 'rewardsystem' ) ,
							'Due'       => __( 'Due' , 'rewardsystem' ) ,
								) ;
						$BtnStatus       = __( 'Cancel' , 'rewardsystem' ) ;
						?>
						<tr>
							<td data-value="<?php echo esc_attr($i) ; ?>"><?php echo esc_attr($i) ; ?></td>
							<td><?php echo wp_kses_post($NickName) ; ?> </td>
							<td><?php echo wp_kses_post($AmntReplacedLog) ; ?></td>
							<td><?php echo wp_kses_post($StatusToDisplay[ $status ]) ; ?></td>
							<td>
								<?php
								if ( 'Paid' == $status ) {
									echo esc_html('-') ;
								} else {
									?>
									<input type="button" class = "cancelbutton" value= "<?php echo esc_html($BtnStatus) ; ?>" data-id="<?php echo esc_attr($Data[ 'id' ]) ; ?>" data-status="<?php echo esc_attr($status) ; ?>"/>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
						$i ++ ;
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="7">
							<div class="pagination pagination-centered"></div>
						</td>
					</tr>
				</tfoot>
			</table>
			<?php
			return ob_get_contents() ;
		}

	}

	RSCashBackFrontend::init() ;
}
