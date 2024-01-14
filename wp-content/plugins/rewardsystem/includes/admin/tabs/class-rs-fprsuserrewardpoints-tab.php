<?php
/**
 * User Reward Points Tab.
 *
 * @package RewardPoints
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'RSUserRewardPoints' ) ) {

	/**
	 * Class Initialization.
	 */
	class RSUserRewardPoints {

		public static function init() {
			add_action( 'woocommerce_rs_settings_tabs_fprsuserrewardpoints', array( __CLASS__, 'reward_system_register_admin_settings' ) ); // Call to register the admin settings in the Reward System Submenu with general Settings tab

			add_action( 'woocommerce_update_options_fprsuserrewardpoints', array( __CLASS__, 'reward_system_update_settings' ) ); // call the woocommerce_update_options_{slugname} to update the reward system

			add_action( 'woocommerce_admin_field_wplisttable_for_log', array( __CLASS__, 'display_log' ) );

			add_action( 'woocommerce_admin_field_user_and_user_role_filter', array( __CLASS__, 'user_and_user_role_filter' ) );
		}

		/**
		 * Function label settings to Member Level Tab
		 */
		public static function reward_system_admin_fields() {
			$role_list = fp_user_roles();
			/**
			 * Hook:woocommerce_fprsuserrewardpoints_settings.
			 *
			 * @since 1.0
			 */
			return apply_filters(
				'woocommerce_fprsuserrewardpoints_settings',
				array(
					array(
						'type' => 'rs_modulecheck_start',
					),
					array(
						'name' => __( 'User Reward Points Log', 'rewardsystem' ),
						'type' => 'title',
						'id'   => 'rs_user_reward_points_setting',
					),
					array(
						'name'     => __( 'Filter by', 'rewardsystem' ),
						'id'       => 'rs_filter_type_for_log',
						'class'    => 'rs_filter_type_for_log',
						'newids'   => 'rs_filter_type_for_log',
						'std'      => '1',
						'defaults' => '1',
						'type'     => 'select',
						'options'  => array(
							'1' => __( 'User(s)', 'rewardsystem' ),
							'2' => __( 'User Role(s)', 'rewardsystem' ),
						),
					),
					array(
						'name'        => __( 'Select the User Role(s)', 'rewardsystem' ),
						'id'          => 'rs_userrole_for_reward_log',
						'css'         => 'min-width:343px;',
						'std'         => '',
						'default'     => '',
						'placeholder' => 'Search for a User Role',
						'type'        => 'multiselect',
						'options'     => $role_list,
						'newids'      => 'rs_userrole_for_reward_log',
					),
					array(
						'type' => 'user_and_user_role_filter',
					),
					array(
						'type' => 'wplisttable_for_log',
					),
					array(
						'type' => 'rs_modulecheck_end',
					),
				)
			);
		}

		/**
		 * Registering Custom Field Admin Settings of SUMO Reward Points in woocommerce admin fields funtion
		 */
		public static function reward_system_register_admin_settings() {
			woocommerce_admin_fields( self::reward_system_admin_fields() );
		}

		/**
		 * Update the Settings on Save Changes may happen in SUMO Reward Points
		 */
		public static function reward_system_update_settings() {
			woocommerce_update_options( self::reward_system_admin_fields() );
		}

		/**
		 * User Search Field.
		 */
		public static function user_and_user_role_filter() {
			$field_id    = 'rs_select_user_for_reward_log';
			$field_label = __( 'Select the User(s)', 'rewardsystem' );
			$getuser     = get_option( 'rs_select_user_for_reward_log' );
			echo do_shortcode( user_selection_field( $field_id, $field_label, $getuser ) );
			?>
			<tr>
				<td></td>
				<td>
					<input type="submit" value="<?php esc_html_e( 'Search', 'rewardsystem' ); ?>" id="rs_submit_for_user_role_log" class="button-primary" name="rs_submit_for_user_role_log">
				</td>
			</tr>
			<?php
		}

		/**
		 * Display User Log Table.
		 */
		public static function display_log() {
			if ( ( ! isset( $_GET['view'] ) ) && ( ! isset( $_GET['edit'] ) ) ) {
				$user_list_table = new WP_List_Table_For_Users();
				$user_list_table->prepare_items();
				$user_list_table->search_box( __( 'Search Users', 'rewardsystem' ), 'search_id' );
				$user_list_table->display();
			} elseif ( isset( $_GET['view'] ) ) {
				$user_log_list_table = new SRP_View_Log_Table();
				$user_log_list_table->prepare_items();
				$user_log_list_table->search_box( __( 'Search', 'rewardsystem' ), 'search_id' );
				$user_log_list_table->display();
				?>
				<a href="<?php echo esc_url( remove_query_arg( array( 'view' ) ) ); ?>"><?php esc_html_e( 'Go Back', 'rewardsystem' ); ?></a>
				<?php
			} else {
				$user_id        = wc_clean( wp_unslash( $_GET['edit'] ) );
				$points_data    = new RS_Points_Data( $user_id );
				$points         = $points_data->total_available_points();
				$points_entered = isset( $_REQUEST['rs_points'] ) ? wc_clean( wp_unslash( $_REQUEST['rs_points'] ) ) : 0;
				$reason         = isset( $_REQUEST['reason_in_detail'] ) ? wc_clean( wp_unslash( $_REQUEST['reason_in_detail'] ) ) : '';
				$table_args     = array(
					'user_id'     => $user_id,
					'checkpoints' => isset( $_REQUEST['rs_add_points_for_user'] ) ? 'MAURP' : 'MRURP',
					'reason'      => $reason,
				);

				if ( isset( $_REQUEST['rs_add_points_for_user'] ) ) {

					$table_args['pointstoinsert']    = $points_entered;
					$table_args['totalearnedpoints'] = $points_entered;

					if ( 'yes' == get_option( 'rs_enable_disable_max_earning_points_for_user' ) ) {
						$object = new RewardPointsOrder( 0, 'no' );
						$object->check_point_restriction( $points_entered, 0, 'MAURP', $user_id, '', '', '', '', $reason );
					} else {
						RSPointExpiry::insert_earning_points( $table_args );
						RSPointExpiry::record_the_points( $table_args );
					}
				}

				if ( isset( $_REQUEST['rs_remove_point_for_user'] ) ) {
					if ( $points_entered <= $points ) {
						$table_args['usedpoints'] = $points_entered;
						RSPointExpiry::perform_calculation_with_expiry( $points_entered, $user_id );
						RSPointExpiry::record_the_points( $table_args );
						$redirect = add_query_arg( 'saved', 'true', get_permalink() );
						wp_safe_redirect( $redirect );
						exit();
					}
				}
				?>
				<h3><?php esc_html_e( 'Update User Reward Points', 'rewardsystem' ); ?></h3>
				<table class="form-table">
					<tr valign ="top">
						<th class="titledesc" scope="row">
							<label><?php esc_html_e( 'Current Points for User', 'rewardsystem' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							<input class="fp-srp-current-points-value" type="number" readonly="readonly" value="<?php echo esc_attr( $points ); ?>"/>
						</td>
					</tr>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label><?php esc_html_e( 'Enter Points', 'rewardsystem' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							<input class="fp-srp-current-points-value" type="number" required='required' min="0" step="any" id="rs_points" name="rs_points">
							<div class='rs_add_remove_points_errors'></div>
						</td>
					</tr>
					<tr valign="top">
						<th class="titledesc" scope="row">
							<label><?php esc_html_e( 'Reason in Detail', 'rewardsystem' ); ?></label>
						</th>
						<td class="forminp forminp-text">
							<textarea cols='40' rows='5' name='reason_in_detail' required='required'></textarea>
						</td>
					</tr>
					<tr valign="top">
						<td>
							<input type='submit' name='rs_add_points_for_user' class='button-primary rs_add_point_for_user' value='<?php esc_html_e( 'Add Points', 'rewardsystem' ); ?>' />
							<input type='hidden' id ="rs_selected_user_id" value='<?php echo esc_html( $user_id ); ?>'>
						</td>
						<td>
							<input type='submit' name='rs_remove_point_for_user' class='button-primary rs_remove_point_for_user' value='<?php esc_html_e( 'Remove Points', 'rewardsystem' ); ?>' />
						</td>
						<td>
							<a href="<?php echo esc_url( remove_query_arg( array( 'edit', 'saved' ) ) ); ?>">
								<input type='button' class='button-primary' value='<?php esc_html_e( 'Go Back', 'rewardsystem' ); ?>'/>
							</a>
						</td>
					</tr>
				</table>
				<?php
			}
		}
	}

	RSUserRewardPoints::init();
}
