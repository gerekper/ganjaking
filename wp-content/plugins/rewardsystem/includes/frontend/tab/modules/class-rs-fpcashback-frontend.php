<?php
/**
 * Cashback Functionality.
 *
 * @package Rewardsystem
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSCashBackFrontend' ) ) {

	/**
	 * Class Initialization.
	 */
	class RSCashBackFrontend {

		/**
		 * Add Hooks.
		 */
		public static function init() {
			add_action( 'woocommerce_after_my_account', array( __CLASS__, 'cash_back_log' ) );
		}

		/**
		 * Display Cashback Logs.
		 */
		public static function cash_back_log() {
			if ( '2' === get_option( 'rs_my_cashback_table' ) ) {
				return;
			}

			if ( 'yes' !== get_option( 'rs_reward_content' ) ) {
				return;
			}

			$table_data = array(
				'title'    => get_option( 'rs_my_cashback_title' ),
				'sno'      => get_option( 'rs_my_cashback_sno_label' ),
				'username' => get_option( 'rs_my_cashback_userid_label' ),
				'request'  => get_option( 'rs_my_cashback_requested_label' ),
				'status'   => get_option( 'rs_my_cashback_status_label' ),
				'action'   => get_option( 'rs_my_cashback_action_label' ),
			);
			self::cash_back_log_table( $table_data );
		}

		/**
		 * Get Cashback Log Table.
		 *
		 * @param array $table_data Table data.
		 */
		public static function cash_back_log_table( $table_data ) {
			$user_id = get_current_user_id();
			if ( 'redeemingonly' === check_banning_type( $user_id ) || 'both' === check_banning_type( $user_id ) ) {
				return;
			}

			global $wpdb;
			$cashback_data = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}sumo_reward_encashing_submitted_data WHERE userid = %d", $user_id ), ARRAY_A );

			ob_start();
			echo wp_kses_post( '<h2 class=rs_my_cashback_title>' . $table_data['title'] . '</h2>' );
			?>
			<table class = "examples demo shop_table my_account_orders table-bordered" data-filter = "#filters" data-page-size="5" data-page-previous-text = "prev" data-filter-text-only = "true" data-page-next-text = "next">
				<thead>
					<tr>
						<th data-toggle="true" data-sort-initial = "true"><?php echo esc_html( $table_data['sno'] ); ?></th>
						<th><?php echo esc_html( $table_data['username'] ); ?></th>
						<th><?php echo esc_html( $table_data['request'] ); ?></th>
						<th><?php echo esc_html( $table_data['status'] ); ?></th>
						<th><?php echo esc_html( $table_data['action'] ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if ( '1' === get_option( 'rs_points_log_sorting' ) ) {
						krsort( $cashback_data, SORT_NUMERIC );
					}

					$i = 1;
					if ( srp_check_is_array( $cashback_data ) ) {
						foreach ( $cashback_data as $data ) {

							if ( ! srp_check_is_array( $data ) ) {
								continue;
							}

							$NickName        = get_user_meta( $data['userid'], 'nickname', true );
							$Log             = get_option( '_rs_localize_points_to_cash_log_in_my_cashback_table' );
							$PointReplaceLog = str_replace( '[pointstocashback]', $data['pointstoencash'], $Log );
							$AmntReplacedLog = str_replace( '[cashbackamount]', get_woocommerce_currency_symbol() . $data['pointsconvertedvalue'], $PointReplaceLog );
							$status          = $data['status'];
							$StatusToDisplay = array(
								'Cancelled' => __( 'Cancelled', 'rewardsystem' ),
								'Paid'      => __( 'Paid', 'rewardsystem' ),
								'Due'       => __( 'Due', 'rewardsystem' ),
							);
							$BtnStatus       = __( 'Cancel', 'rewardsystem' );
							?>
						<tr>
							<td data-value="<?php echo esc_attr( $i ); ?>"><?php echo esc_attr( $i ); ?></td>
							<td><?php echo esc_html( $NickName ); ?> </td>
							<td><?php echo esc_html( $AmntReplacedLog ); ?></td>
							<td><?php echo esc_html( $StatusToDisplay[ $status ] ); ?></td>
							<td>
								<?php
								if ( 'Paid' == $status ) {
									echo esc_html( '-' );
								} else {
									?>
									<input type="button" class = "cancelbutton" value= "<?php echo esc_html( $BtnStatus ); ?>" data-id="<?php echo esc_attr( $data['id'] ); ?>" data-status="<?php echo esc_attr( $status ); ?>"/>
									<?php
								}
								?>
							</td>
						</tr>
							<?php
							$i++;
						}
					} else {
						?>
						<tr>
							<td colspan="4">
								<?php esc_html_e( 'No request found', 'rewardsystem' ); ?>
							</td>
						</tr>
						<?php
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
			return ob_get_contents();
		}
	}

	RSCashBackFrontend::init();
}
