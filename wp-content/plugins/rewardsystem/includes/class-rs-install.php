<?php
/*
 * Trigger this upon plugin install
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RSInstall' ) ) {

	class RSInstall {

		private static $dbversion = '1.2.3';

		/**
		 * Check current version of the plugin is updated when activating plugin, if not run updater.
		 */
		public static function check_version() {
			if ( version_compare( get_option( 'srp_version' ), SRP_VERSION, '>=' ) ) {
				return;
			}

			self::update_version();
			self::set_default_value_for_tab();
			self::set_default_value_for_modules();
		}

		/**
		 * Update current version.
		 */
		private static function update_version() {
			update_option( 'srp_version', SRP_VERSION );
		}

		/**
		 * Assign Default Values for All Tab
		 */
		public static function set_default_value_for_tab() {
			$tabs = array(
				'fprsgeneral',
				'fprsmodules',
				'fprsaddremovepoints',
				'fprsmessage',
				'fprslocalization',
				'fprsuserrewardpoints',
				'fprsmasterlog',
				'fprsadvanced',
			);
						/**
						 * Hook:rs_default_value_tabs.
						 *
						 * @since 1.0
						 */
			$tabs = apply_filters( 'rs_default_value_tabs', $tabs );
			if ( ! srp_check_is_array( $tabs ) ) {
				return;
			}

			foreach ( $tabs as $tab ) {

				include_once SRP_PLUGIN_PATH . '/includes/admin/tabs/class-rs-' . $tab . '-tab.php';
								/**
								 * Hook:rs_default_settings.
								 *
								 * @since 1.0
								 */
				do_action( 'rs_default_settings_' . $tab );
			}
		}

		/**
		 * Assign Default Values for All Modules
		 */
		public static function set_default_value_for_modules() {
			$modules = array(
				'fpproductpurchase',
				'fpreferralsystem',
				'fpsocialreward',
				'fpactionreward',
				'fppointexpiry',
				'fpredeeming',
				'fppointprice',
				'fpsocialreward',
				'fpgiftvoucher',
				'fpmail',
				'fpsms',
			);
						/**
						 * Hook:rs_default_value_modules.
						 *
						 * @since 1.0
						 */
			$modules = apply_filters( 'rs_default_value_modules', $modules );
			if ( ! srp_check_is_array( $modules ) ) {
				return;
			}

			foreach ( $modules as $module ) {
				// include current page functionality.
				include_once SRP_PLUGIN_PATH . '/includes/admin/tabs/modules/class-rs-' . $module . '-module-tab.php';
								/**
								 * Hook:rs_default_settings.
								 *
								 * @since 1.0
								 */
				do_action( 'rs_default_settings_' . $module );
			}
		}

		public static function get_charset_table() {
			global $wpdb;
			$charset_collate = $wpdb->has_cap( 'collation' ) ? $wpdb->get_charset_collate() : '';
			return $charset_collate;
		}

		public static function create_table_for_point_expiry() {
			global $wpdb;
						$table_name = "{$wpdb->prefix}rspointexpiry";
			if ( self::rs_check_table_exists( $table_name ) ) {
				$charset_collate = self::get_charset_table();
				$sql             = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		earnedpoints FLOAT,
                usedpoints FLOAT,
                expiredpoints FLOAT,
                userid INT(99),
                earneddate VARCHAR(999) NOT NULL,
                expirydate VARCHAR(999) NOT NULL,
                checkpoints VARCHAR(999) NOT NULL,
                orderid INT(99),
                totalearnedpoints INT(99),
                totalredeempoints INT(99),
                reasonindetail VARCHAR(999),
         	UNIQUE KEY id (id)
                ) $charset_collate;";
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
				add_option( 'rs_point_expiry', self::$dbversion );
			}
			if ( ! self::rs_check_table_exists( $table_name ) ) {
				if ( ! self::rs_check_column_exists( $table_name, 'totalearnedpoints' ) ) {
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}rspointexpiry MODIFY totalearnedpoints FLOAT" );
				}
				if ( ! self::rs_check_column_exists( $table_name, 'totalredeempoints' ) ) {
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}rspointexpiry MODIFY totalredeempoints FLOAT" );
				}
			}
		}

		public static function rs_update_null_value_to_zero() {
			global $wpdb;
			$querys = $wpdb->get_results( "SELECT id,usedpoints FROM {$wpdb->prefix}rspointexpiry WHERE usedpoints IS NULL", ARRAY_A );
			foreach ( $querys as $query ) {
				$wpdb->update( "{$wpdb->prefix}rspointexpiry", array( 'usedpoints' => 0 ), array( 'id' => $query['id'] ) );
			}
		}

		public static function create_table_to_record_earned_points_and_redeem_points() {

			global $wpdb;
			$getdbversiondata = 'false' != get_option( 'rs_record_points' ) ? get_option( 'rs_record_points' ) : '0';
			$table_name       = "{$wpdb->prefix}rsrecordpoints";
			if ( self::rs_check_table_exists( $table_name ) ) {
				$charset_collate = self::get_charset_table();
				$sql             = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		earnedpoints FLOAT,
                redeempoints FLOAT,
                userid INT(99),
                earneddate VARCHAR(999) NOT NULL,
                expirydate VARCHAR(999) NOT NULL,
                checkpoints VARCHAR(999) NOT NULL,
                earnedequauivalentamount INT(99),
                redeemequauivalentamount INT(99),
                orderid INT(99),
                productid INT(99),
                variationid INT(99),
                refuserid INT(99),
                reasonindetail VARCHAR(999),
                totalpoints INT(99),
                showmasterlog VARCHAR(999),
                showuserlog VARCHAR(999),
                nomineeid INT(99),
                nomineepoints INT(99),
         	UNIQUE KEY id (id)
                ) $charset_collate;";
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
				add_option( 'rs_record_points', self::$dbversion );
			}
			if ( ! self::rs_check_table_exists( $table_name ) ) {
				if ( ! self::rs_check_column_exists( $table_name, 'redeemequauivalentamount' ) ) {
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}rsrecordpoints MODIFY redeemequauivalentamount FLOAT " );
				}
				if ( ! self::rs_check_column_exists( $table_name, 'totalpoints' ) ) {
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}rsrecordpoints MODIFY totalpoints FLOAT " );
				}
				if ( ! self::rs_check_column_exists( $table_name, 'earnedequauivalentamount' ) ) {
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}rsrecordpoints MODIFY earnedequauivalentamount FLOAT " );
				}
			}
		}

		public static function create_table_for_gift_voucher() {
			global $wpdb;
			$table_name = "{$wpdb->prefix}rsgiftvoucher";
			if ( self::rs_check_table_exists( $table_name ) ) {
				$charset_collate = self::get_charset_table();
				$sql             = "CREATE TABLE IF NOT EXISTS $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		vouchercode VARCHAR(999) NOT NULL,
                points FLOAT,
                vouchercreated VARCHAR(999) NOT NULL,
                voucherexpiry VARCHAR(999) NOT NULL,
                memberused VARCHAR(999) NOT NULL,
         	UNIQUE KEY id (id)
                ) $charset_collate;";
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
			if ( ! self::rs_check_table_exists( $table_name ) ) {
				if ( self::rs_check_column_exists( $table_name, 'voucher_code_usage' ) ) {
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}rsgiftvoucher ADD voucher_code_usage VARCHAR(20) NOT NULL" );
				}
				if ( self::rs_check_column_exists( $table_name, 'voucher_code_usage_limit' ) ) {
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}rsgiftvoucher ADD voucher_code_usage_limit VARCHAR(20) NOT NULL" );
				}
				if ( self::rs_check_column_exists( $table_name, 'voucher_code_usage_limit_val' ) ) {
					$wpdb->query( "ALTER TABLE {$wpdb->prefix}rsgiftvoucher ADD voucher_code_usage_limit_val INT(20) NOT NULL" );
				}
			}
		}

		public static function create_table_for_email_template() {
			global $wpdb;
			$getdbversiondata = false !== get_option( 'rs_email_template_version' ) ? get_option( 'rs_email_template_version' ) : '0';
			$table_name       = "{$wpdb->prefix}rs_templates_email";
			if ( self::rs_check_table_exists( $table_name ) ) {
				$charset_collate = self::get_charset_table();
				$sql             = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                template_name LONGTEXT NOT NULL,
                sender_opt VARCHAR(10) NOT NULL DEFAULT 'woo',
                from_name LONGTEXT NOT NULL,
                from_email LONGTEXT NOT NULL,
                subject LONGTEXT NOT NULL,
                message LONGTEXT NOT NULL,
                earningpoints LONGTEXT NOT NULL,
                redeemingpoints LONGTEXT NOT NULL,
                mailsendingoptions LONGTEXT NOT NULL,
                rsmailsendingoptions LONGTEXT NOT NULL,
                minimum_userpoints LONGTEXT NOT NULL,
                sendmail_options VARCHAR(10) NOT NULL DEFAULT '1',
                sendmail_to LONGTEXT NOT NULL,
                sending_type VARCHAR(20) NOT NULL,
                rs_status VARCHAR(20) NOT NULL DEFAULT 'DEACTIVATE',
                UNIQUE KEY id (id)
                ) $charset_collate;";
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
				add_option( 'rs_email_template_version', self::$dbversion );
			}
			if ( ! self::rs_check_table_exists( $table_name ) && self::rs_check_column_exists( $table_name, 'rs_status' ) ) {
								$wpdb->query( $wpdb->prepare( "ALTER TABLE {$wpdb->prefix}rs_templates_email ADD rs_status VARCHAR(20) NOT NULL DEFAULT %s", 'DEACTIVATE' ) );
			}
		}

		public static function create_table_for_encash_reward_points() {
			global $wpdb;
			$getdbversiondata = get_option( 'rs_encash_version' ) != 'false' ? get_option( 'rs_encash_version' ) : '0';
			$table_name       = "{$wpdb->prefix}sumo_reward_encashing_submitted_data";
			if ( self::rs_check_table_exists( $table_name ) ) {
				$charset_collate = self::get_charset_table();
				$sql             = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                userid INT(225),
                userloginname VARCHAR(200),
                pointstoencash VARCHAR(200),
                pointsconvertedvalue VARCHAR(200),
                encashercurrentpoints VARCHAR(200),
                reasonforencash LONGTEXT,
                encashpaymentmethod VARCHAR(200),
                paypalemailid VARCHAR(200),
                otherpaymentdetails LONGTEXT,
                status VARCHAR(200),
                date VARCHAR(300),
                UNIQUE KEY id (id)
                ) $charset_collate;";
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
				add_option( 'rs_encash_version', self::$dbversion );
			}
		}

		public static function create_table_for_send_points() {
			global $wpdb;
			$getdbversiondata = 'false' != get_option( 'rs_send_points_version' ) ? get_option( 'rs_send_points_version' ) : '0';
			$table_name       = "{$wpdb->prefix}sumo_reward_send_point_submitted_data";
			if ( self::rs_check_table_exists( $table_name ) ) {
				$charset_collate = self::get_charset_table();
				$sql             = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                userid INT(225),
                userloginname VARCHAR(200),
                pointstosend VARCHAR(200),
                sendercurrentpoints VARCHAR(200),
                status VARCHAR(200),
                selecteduser LONGTEXT NOT NULL,
                date VARCHAR(300),
                UNIQUE KEY id (id)
                ) $charset_collate;";
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
				add_option( 'rs_send_points_version', self::$dbversion );
			}
		}

		public static function insert_data_for_email_template() {
			global $wpdb;
			$email_temp_check = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rs_templates_email", ARRAY_A );
			if ( srp_check_is_array( $email_temp_check ) ) {
				return;
			}

			return $wpdb->insert(
				"{$wpdb->prefix}rs_templates_email",
				array(
					'template_name'        => 'Default',
					'sender_opt'           => 'woo',
					'from_name'            => 'Admin',
					'from_email'           => get_option( 'admin_email' ),
					'subject'              => 'SUMO Rewards Point',
					'message'              => 'Hi {rsfirstname} {rslastname}, <br><br> You have Earned Reward Points: {rspoints} on {rssitelink}  <br><br> You can use this Reward Points to make discounted purchases on {rssitelink} <br><br> Thanks',
					'minimum_userpoints'   => '0',
					'mailsendingoptions'   => '2',
					'rsmailsendingoptions' => '3',
				)
			);
		}

		public static function install() {
			self::create_table_for_point_expiry();
			self::create_table_to_record_earned_points_and_redeem_points();
			self::create_table_for_email_template();
			self::create_table_for_email_template_expired_point();
			self::create_table_for_gift_voucher();
			self::create_table_for_encash_reward_points();
			self::create_table_for_send_points();
			self::rs_update_null_value_to_zero();
			self::insert_data_for_email_template();
			self::insert_data_for_email_template_for_expiry();
			self::default_value_for_earning_and_redeem_points();
			self::enable_newly_added_module();
		}

		/**
		 * Function for send mail based on cron time
		 */
		public static function create_table_for_email_template_expired_point() {
			global $wpdb;
			$table_name = "{$wpdb->prefix}rs_expiredpoints_email";
			if ( self::rs_check_table_exists( $table_name ) ) {
				$charset_collate = self::get_charset_table();
				$sql             = "CREATE TABLE IF NOT EXISTS $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                template_name LONGTEXT NOT NULL,
                sender_opt VARCHAR(10) NOT NULL DEFAULT 'woo',
                from_name LONGTEXT NOT NULL,
                from_email LONGTEXT NOT NULL,
                subject LONGTEXT NOT NULL,
                message LONGTEXT NOT NULL,
                noofdays FLOAT,
                rs_status VARCHAR(20) NOT NULL DEFAULT 'DEACTIVATE',
                UNIQUE KEY id (id)
                ) $charset_collate;";
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );
			}
		}

		public static function insert_data_for_email_template_for_expiry() {
			global $wpdb;
			$email_temp_check = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}rs_expiredpoints_email", OBJECT );
			if ( empty( $email_temp_check ) ) {
				return $wpdb->insert(
					"{$wpdb->prefix}rs_expiredpoints_email",
					array(
						'template_name' => 'Default',
						'sender_opt'    => 'woo',
						'from_name'     => 'Admin',
						'from_email'    => get_option( 'admin_email' ),
						'subject'       => 'SUMO Rewards Point',
						'message'       => 'Hi {rsfirstname} {rslastname}, <br><br>Please check the below table which shows about your earned points with an expiry date. You can make use of those points to get discount on future purchases in {rssitelink} <br><br> {rs_points_expire} <br><br> Thanks',
						'noofdays'      => '',
						'rs_status'     => 'DEACTIVATE',
					)
				);
			}
		}

		public static function rs_check_column_exists( $table_name, $column_name ) {
			global $wpdb;
			$data_base     = constant( 'DB_NAME' );
			$column_exists = $wpdb->query( $wpdb->prepare( 'select * from information_schema.columns where table_schema= %s and table_name = %s and column_name = %s', $data_base, $table_name, $column_name ) );
			return ( 0 === $column_exists ) ? true : false;
		}

		public static function rs_check_table_exists( $table_name ) {
			global $wpdb;
			$data_base     = constant( 'DB_NAME' );
			$column_exists = $wpdb->query( $wpdb->prepare( 'select * from information_schema.columns where table_schema= %s and table_name = %s', $data_base, $table_name ) );
			if ( 0 === $column_exists ) {
				add_option( 'rs_new_update_user', true );
				return true; // if not exists return true.
			}
			return false; // if it is exists return false.
		}

		public static function default_value_for_earning_and_redeem_points() {
			add_option( 'rs_earn_point', '1' );
			add_option( 'rs_earn_point_value', '1' );
			add_option( 'rs_redeem_point', '1' );
			add_option( 'rs_redeem_point_value', '1' );
			add_option( 'rs_redeem_point_for_cash_back', '1' );
			add_option( 'rs_redeem_point_value_for_cash_back', '1' );
		}

		public static function enable_newly_added_module() {
			global $wpdb;
			if ( self::rs_check_table_exists( "{$wpdb->prefix}rspointexpiry" ) ) {
				return;
			}

			$enabledcount = self::is_buying_enabled();
			if ( $enabledcount > 0 && ! ( get_option( 'rs_buyingpoints_activated' ) ) ) {
				update_option( 'rs_buyingpoints_activated', 'yes' );
			}
		}

		public static function is_buying_enabled() {
			global $wpdb;
			$simple_product_ids   = $wpdb->get_col( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_rewardsystem_buying_reward_points' AND meta_value = 'yes'" );
			$variable_product_ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_rewardsystem_buying_reward_points' AND meta_value = '1'" );
			return count( $simple_product_ids ) + count( $variable_product_ids );
		}
	}

}
