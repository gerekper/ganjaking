<?php
/*
 * Import Export Tab Setting
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit ; // Exit if accessed directly.
}
if ( ! class_exists( 'RSImportExport' ) ) {

	class RSImportExport {

		public static function init() {
			add_action( 'woocommerce_rs_settings_tabs_fpimportexport' , array( __CLASS__, 'register_admin_settings' ) ) ;

			add_action( 'woocommerce_update_options_fprsmodules_fpimportexport' , array( __CLASS__, 'update_settings' ) ) ;

			add_action( 'woocommerce_admin_field_rs_import_export_selected_user' , array( __CLASS__, 'user_selection_field' ) ) ;

			add_action( 'woocommerce_admin_field_import_export' , array( __CLASS__, 'settings_to_impexp_points' ) ) ;

			add_action( 'woocommerce_admin_field_rs_enable_disable_imp_exp_module' , array( __CLASS__, 'enable_module' ) ) ;

			add_action( 'rs_default_settings_fpimportexport' , array( __CLASS__, 'set_default_value' ) ) ;

			add_action( 'fp_action_to_reset_module_settings_fpimportexport' , array( __CLASS__, 'reset_imp_exp_module' ) ) ;
		}

		public static function settings_option() {
			/**
			 * Hook:woocommerce_rewardsystem_gift_voucher_settings.
			 * 
			 * @since 1.0
			 */
			return apply_filters( 'woocommerce_rewardsystem_gift_voucher_settings' , array(
				array(
					'type' => 'rs_modulecheck_start',
				),
				array(
					'name' => __( 'Import/Export Points Module' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_activate_imp_exp_module',
				),
				array(
					'type' => 'rs_enable_disable_imp_exp_module',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_activate_imp_exp_module' ),
				array(
					'type' => 'rs_modulecheck_end',
				),
				array(
					'type' => 'rs_wrapper_start',
				),
				array(
					'name' => __( 'Import/Export User Points in CSV Format' , 'rewardsystem' ),
					'type' => 'title',
					'id'   => '_rs_import_export_setting',
				),
				array(
					'name'     => __( 'Export available Points for' , 'rewardsystem' ),
					'desc'     => __( 'Here you can set whether to Export Reward Points for All Users / Selected User(s ) / Selected User Role(s)' , 'rewardsystem' ),
					'id'       => 'rs_export_import_user_option',
					'std'      => '1',
					'default'  => '1',
					'type'     => 'radio',
					'options'  => array(
						'1' => esc_html__( 'All Users' , 'rewardsystem' ),
						'2' => esc_html__( 'Selected User(s)' , 'rewardsystem' ),
						'3' => esc_html__( 'Selected User Role(s)' , 'rewardsystem' ),
					),
					'newids'   => 'rs_export_import_user_option',
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Select the User(s) for whom you wish to Export Points' , 'rewardsystem' ),
					'desc'     => __( 'Here you select the users to whom you wish to Export Reward Points' , 'rewardsystem' ),
					'id'       => 'rs_import_export_users_list',
					'std'      => '',
					'default'  => '',
					'type'     => 'rs_import_export_selected_user',
					'newids'   => 'rs_import_export_users_list',
					'desc_tip' => true,
				),
				array(
					'name'        => __( 'Select the User Role(s)' , 'rewardsystem' ),
					'id'          => 'rs_export_user_roles',
					'css'         => 'min-width:343px;',
					'std'         => '',
					'default'     => '',
					'placeholder' => 'Search for a User Role',
					'type'        => 'multiselect',
					'options'     => fp_user_roles(),
					'newids'      => 'rs_export_user_roles',
					'desc_tip'    => false,
				),
				array(
					'name'     => __( 'Users are identified based on' , 'rewardsystem' ),
					'desc'     => __( 'Here you can set whether to Export CSV Format with Username or Userid or Emailid' , 'rewardsystem' ),
					'id'       => 'rs_csv_format',
					'class'    => 'rs_csv_format',
					'newids'   => 'rs_csv_format',
					'std'      => '1',
					'default'  => '1',
					'type'     => 'radio',
					'options'  => array( '1' => __( 'Username' , 'rewardsystem' ), '2' => __( 'Email-Id' , 'rewardsystem' ) ),
					'desc_tip' => true,
				),
				array(
					'name'     => __( 'Export User Points for' , 'rewardsystem' ),
					'desc'     => __( 'Here you can set whether to Export Reward Points for All Time or Selected Date' , 'rewardsystem' ),
					'id'       => 'rs_export_import_date_option',
					'class'    => 'rs_export_import_date_option',
					'std'      => '1',
					'default'  => '1',
					'type'     => 'radio',
					'options'  => array( '1' => __( 'All Time' , 'rewardsystem' ), '2' => __( 'Selected Date' , 'rewardsystem' ) ),
					'newids'   => 'rs_export_import_date_option',
					'desc_tip' => true,
				),
				array(
					'type' => 'import_export',
				),
				array( 'type' => 'sectionend', 'id' => '_rs_import_export_setting' ),
				array(
					'type' => 'rs_wrapper_end',
				),
					) ) ;
		}

		public static function register_admin_settings() {
			woocommerce_admin_fields( self::settings_option() ) ;
		}

		public static function update_settings() {
			woocommerce_update_options( self::settings_option() ) ;
			if ( isset( $_REQUEST[ 'rs_imp_exp_module_checkbox' ] ) ) {
				update_option( 'rs_imp_exp_activated' , wc_clean( wp_unslash( $_REQUEST[ 'rs_imp_exp_module_checkbox' ] ) ) ) ;
			} else {
				update_option( 'rs_imp_exp_activated' , 'no' ) ;
			}
		}

		public static function set_default_value() {
			foreach ( self::settings_option() as $setting ) {
				if ( isset( $setting[ 'newids' ] ) && isset( $setting[ 'std' ] ) ) {
					add_option( $setting[ 'newids' ] , $setting[ 'std' ] ) ;
				}
			}
		}

		public static function enable_module() {
			RSModulesTab::checkbox_for_module( get_option( 'rs_imp_exp_activated' ) , 'rs_imp_exp_module_checkbox' , 'rs_imp_exp_activated' ) ;
		}

		public static function user_selection_field() {
			$field_id    = 'rs_import_export_users_list' ;
			$field_label = __( 'Select the Users that you wish to Export Reward Points' , 'rewardsystem' ) ;
			$getuser     = get_option( 'rs_import_export_users_list' ) ;
			echo do_shortcode( user_selection_field( $field_id , $field_label , $getuser ) ) ;
		}

		public static function settings_to_impexp_points() {
			if ( isset( $_REQUEST[ 'rs_import_user_points' ] ) || isset( $_REQUEST[ 'rs_import_user_points_old' ] ) ) {
				self::imp_user_points() ;
			}

			if ( isset( $_REQUEST[ 'fp_bg_process' ] ) ) {
				delete_option( 'rs_import_points_background_updater_offset' ) ;
				delete_option( 'rewardsystem_csv_array' ) ;
				delete_option( 'rs_import_points_checkpoint' ) ;
				$redirect_url = esc_url_raw( add_query_arg( array( 'page' => 'rewardsystem_callback', 'tab' => 'fprsmodules', 'section' => 'fpimportexport' ) , SRP_ADMIN_URL ) ) ;
				wp_safe_redirect( $redirect_url ) ;
				exit ;
			}

			$DataToImport = get_option( 'rewardsystem_csv_array' ) ;
			?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="rs_point_export_start_date"><?php esc_html_e( 'Start Date' , 'rewardsystem' ) ; ?></label>
				</th>
				<td class="forminp forminp-select">
					<input type="text" class="rs_point_export_start_date" value="" name="rs_point_export_start_date" id="rs_point_export_start_date" />
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="rs_point_export_end_date"><?php esc_html_e( 'End Date' , 'rewardsystem' ) ; ?></label>
				</th>
				<td class="forminp forminp-select">
					<input type="text" class="rs_point_export_end_date" value="" name="rs_point_export_end_date" id="rs_point_export_end_date" />
				</td>
			</tr>
			<tr valign ="top">
				<th class="titledesc" scope="row">
					<label><?php esc_html_e( 'Export User Points to CSV' , 'rewardsystem' ) ; ?></label>
				</th>
				<td class="forminp forminp-select">
					<input type="button" id="rs_export_user_points_csv" class="rs_export_button" name="rs_export_user_points_csv" value="<?php esc_html_e( 'Export User Points' , 'rewardsystem' ) ; ?>"/>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="rs_import_user_points_csv"><?php esc_html_e( 'Import User Points to CSV' , 'rewardsystem' ) ; ?></label>
				</th>
				<td class="forminp forminp-select">
					<input type="file" id="rs_import_user_points_csv" name="file" />
				</td>
			</tr>
			<tr class="rs_import_button" valign="top">
				<td class="forminp forminp-select">
					<input type="submit" id="rs_import_user_points" class="rs_export_button" name="rs_import_user_points" value="<?php esc_html_e( 'Import CSV for Version 10.0 (Above 10.0)' , 'rewardsystem' ) ; ?>"/>
				</td>
				<td class="forminp forminp-select">
					<input type="submit" id="rs_import_user_points_old" class="rs_export_button" name="rs_import_user_points_old" value="<?php esc_html_e( 'Import CSV for Older Version (Below 10.0)' , 'rewardsystem' ) ; ?>"/>
				</td>
			</tr>
			<?php if ( srp_check_is_array( $DataToImport ) ) { ?>
				<table class="wp-list-table widefat fixed posts">
					<thead>
						<tr>
							<th>
								<?php esc_html_e( 'Username' , 'rewardsystem' ) ; ?>
							</th>
							<th>
								<?php esc_html_e( 'User Reward Points' , 'rewardsystem' ) ; ?>
							</th>
							<th>
								<?php esc_html_e( 'Expiry Date' , 'rewardsystem' ) ; ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $DataToImport as $newcsv ) {
							?>
							<tr>
								<td>
									<?php echo esc_html( ( isset( $newcsv[ 0 ] ) && ! empty( $newcsv[ 0 ] ) ) ? $newcsv[ 0 ] : ''  ) ; ?>
								</td>
								<td>
									<?php echo esc_html( ( isset( $newcsv[ 1 ] ) && ! empty( $newcsv[ 1 ] ) ) ? $newcsv[ 1 ] : '0' ) ; ?>
								</td>
								<td>
									<?php
									if ( isset( $newcsv[ 2 ] ) ) {
										$date = ( '999999999999' == $newcsv[ 2 ] ) ? '-' : gmdate( 'm/d/Y h:i:s A T' , $newcsv[ 2 ] ) ;
										echo esc_html( $date ) ;
									} else {
										echo esc_html( '-' ) ;
									}
									?>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<table>
					<tr valign="top">
						<td>
							<input type="submit" id="rs_new_action_reward_points" name="rs_new_action_reward_points" value="<?php esc_html_e( 'Override Existing User Points' , 'rewardsystem' ) ; ?>"/>
						</td>
						<td>
							<input type="submit" id="rs_exist_action_reward_points" name="rs_exist_action_reward_points" value="<?php esc_html_e( 'Add Points with Already Earned Points' , 'rewardsystem' ) ; ?>"/>
						</td>
					</tr>
				</table>
				<?php
			}

			if ( isset( $_GET[ 'export_points' ] ) && 'yes' == wc_clean( wp_unslash( $_GET[ 'export_points' ] ) ) ) {
				ob_end_clean() ;
				header( 'Content-type: text/csv;charset=utf-8' ) ;
				header( 'Content-Disposition: attachment; filename=reward_points_' . date_i18n( 'Y-m-d' ) . '.csv' ) ;
				header( 'Pragma: no-cache' ) ;
				header( 'Expires: 0' ) ;
				$data = get_option( 'rs_data_to_impexp' ) ;
				self::outputCSV( $data ) ;
				exit() ;
			}
		}

		public static function imp_user_points() {
			$files      = $_FILES ;
			$file_error = isset( $files[ 'file' ][ 'error' ] ) ? ( $files[ 'file' ][ 'error' ] ) : 0 ;
			if ( $file_error > 0 ) {
				echo esc_html( 'Error: ' . $file_error . '<br>' ) ;
			} else {
				$mimes = array(
				'text/csv',
					'text/plain',
					'application/csv',
					'text/comma-separated-values',
					'application/excel',
					'application/vnd.ms-excel',
					'application/vnd.msexcel',
					'text/anytext',
					'application/octet-stream',
					'application/txt',
				) ;
				if ( isset( $files[ 'file' ][ 'type' ] ) && in_array( $files[ 'file' ][ 'type' ] , $mimes ) ) {
					$file_name = isset( $files[ 'file' ][ 'tmp_name' ] ) ? $files[ 'file' ][ 'tmp_name' ] : '' ;
					if ( $file_name ) {
						self::inputCSV( $file_name ) ;
					}
				} else {
					$contents = 'div.error {
								display:block;
							}' ;
					wp_register_style( 'fp-srp-importexport-style' , false , array() , SRP_VERSION ) ; // phpcs:ignore
					wp_enqueue_style( 'fp-srp-importexport-style' ) ;
					wp_add_inline_style( 'fp-srp-importexport-style' , $contents ) ;
				}
			}
		}

		public static function outputCSV( $data ) {
			$output = fopen( 'php://output' , 'w' ) ;
			foreach ( $data as $row ) {
				if ( false != $row ) {
					fputcsv( $output , $row ) ; // here you can change delimiter/enclosure
				}
			}
			fclose( $output ) ;
		}

		public static function inputCSV( $data_path ) {
			$handle = file_exists( $data_path ) ? fopen( $data_path , 'r' ) : '' ;
			if ( ! ( $handle ) ) {
				return ;
			}

			while ( ( $data = fgetcsv( $handle , 1000 , ',' ) ) !== false ) {
				$datas        = ( ! empty( $data[ 2 ] ) ) ? strtotime( $data[ 2 ] ) : '' ;
				$collection[] = array_filter( array( $data[ 0 ], $data[ 1 ], $datas ) ) ;
			}
			update_option( 'rewardsystem_csv_array' , array_merge( array_filter( $collection ) ) ) ;
			fclose( $handle ) ;
		}

		public static function reset_imp_exp_module() {
			$settings = self::settings_option() ;
			RSTabManagement::reset_settings( $settings ) ;
		}
	}

	RSImportExport::init() ;
}
