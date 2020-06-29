<?php
/**
 * Plugin Name: YITH Live Chat Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-live-chat/
 * Description: <code><strong>YITH Live Chat Premium</strong></code>is the plugin that allows you to chat with your customers! <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-live-chat
 * Domain Path: /languages/
 * Version: 1.4.5
 * WC tested up to: x.x.x
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}

yit_deactive_free_version( 'YLC_FREE_INIT', plugin_basename( __FILE__ ) );

if ( function_exists( 'yith_deactive_jetpack_module' ) ) {
	global $yith_jetpack_1;
	yith_deactive_jetpack_module( $yith_jetpack_1, 'YLC_PREMIUM', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YLC_VERSION' ) ) {
	define( 'YLC_VERSION', '1.4.5' );
}

if ( ! defined( 'YLC_INIT' ) ) {
	define( 'YLC_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YLC_SLUG' ) ) {
	define( 'YLC_SLUG', 'yith-live-chat' );
}

if ( ! defined( 'YLC_SECRET_KEY' ) ) {
	define( 'YLC_SECRET_KEY', '12345' );
}

if ( ! defined( 'YLC_PREMIUM' ) ) {
	define( 'YLC_PREMIUM', '1' );
}

if ( ! defined( 'YLC_FILE' ) ) {
	define( 'YLC_FILE', __FILE__ );
}

if ( ! defined( 'YLC_DIR' ) ) {
	define( 'YLC_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YLC_URL' ) ) {
	define( 'YLC_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YLC_ASSETS_URL' ) ) {
	define( 'YLC_ASSETS_URL', YLC_URL . 'assets' );
}

if ( ! defined( 'YLC_TEMPLATE_PATH' ) ) {
	define( 'YLC_TEMPLATE_PATH', YLC_DIR . 'templates' );
}

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YLC_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YLC_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YLC_DIR );

function ylc_premium_init() {

	/* Load text domain */
	load_plugin_textdomain( 'yith-live-chat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	YITH_Live_Chat();

}

add_action( 'ylc_premium_init', 'ylc_premium_init' );

function ylc_premium_install() {

	do_action( 'ylc_premium_init' );

}

add_action( 'plugins_loaded', 'ylc_premium_install', 11 );

/**
 * Init default plugin settings
 */
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );
register_activation_hook( __FILE__, 'ylc_roles' );
register_activation_hook( __FILE__, 'ylc_create_tables' );

if ( ! function_exists( 'YITH_Live_Chat' ) ) {

	/**
	 * Unique access to instance of YIYH_Live_Chat
	 *
	 * @since   1.1.0
	 * @return  YITH_Livechat|YITH_Livechat_Premium
	 * @author  Alberto Ruggiero
	 */
	function YITH_Live_Chat() {

		// Load required classes and functions
		require_once( YLC_DIR . 'class.yith-livechat.php' );

		if ( defined( 'YLC_PREMIUM' ) && file_exists( YLC_DIR . 'class.yith-livechat-premium.php' ) ) {

			require_once( YLC_DIR . 'class.yith-livechat-premium.php' );

			return YITH_Livechat_Premium::get_instance();
		}

		return YITH_Livechat::get_instance();

	}

}

if ( ! function_exists( 'ylc_roles' ) ) {

	/**
	 * Initialize Roles
	 *
	 * @since   1.0.0
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	function ylc_roles() {

		YITH_Live_Chat()->ylc_operator_role( 'editor' );

		//Administration role
		$admin_role = get_role( 'administrator' );
		$admin_role->add_cap( 'answer_chat' );

		//Chat Operator role
		$op_role = get_role( 'ylc_chat_op' );
		$op_role->add_cap( 'answer_chat' );

	}

}

if ( ! function_exists( 'ylc_create_tables' ) ) {

	/**
	 * Creates database tables
	 *
	 * @since   1.0.0
	 * @return  void
	 * @author  Alberto Ruggiero
	 */
	function ylc_create_tables() {

		//If exists ylc_db_version option return null
		if ( get_option( 'ylc_db_version' ) ) {
			return;
		}

		// Check if dbDelta() exists
		if ( ! function_exists( 'dbDelta' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		}

		global $wpdb;

		$wpdb->hide_errors();

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {

			if ( ! empty( $wpdb->charset ) ) {

				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";

			}

			if ( ! empty( $wpdb->collate ) ) {

				$collate .= " COLLATE $wpdb->collate";

			}

		}

		$ylc_tables = "
                CREATE TABLE {$wpdb->prefix}ylc_offline_messages (
                id                  int                     NOT NULL    AUTO_INCREMENT,
                user_name           longtext                NOT NULL,
                user_email          longtext                NOT NULL,
                user_message        longtext                NOT NULL,
                user_info           longtext                NOT NULL,
                mail_date           date                    NOT NULL    DEFAULT '0000-00-00',
                mail_read           boolean                 NOT NULL    DEFAULT false,
                PRIMARY KEY (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}ylc_chat_rows (
                  message_id        varchar(30)             NOT NULL    DEFAULT '',
                  conversation_id   varchar(30)             NOT NULL,
                  user_id           varchar(30)             NOT NULL    DEFAULT '',
                  user_name         varchar(32)                         DEFAULT NULL,
                  msg               text                    NOT NULL,
                  msg_time          bigint(13)  unsigned    NOT NULL,
                  UNIQUE KEY message_id (message_id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}ylc_chat_sessions (
                  conversation_id   varchar(30)             NOT NULL    DEFAULT '',
                  user_id           varchar(30)             NOT NULL    DEFAULT '',
                  evaluation        varchar(30)             NOT NULL    DEFAULT '',
                  created_at        bigint(13)  unsigned    NOT NULL,
                  duration          varchar(30)             NOT NULL    DEFAULT '00:00:00',
                  receive_copy      boolean                 NOT NULL    DEFAULT false,
                  UNIQUE KEY conversation_id (conversation_id),
                  KEY created_at (created_at)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}ylc_chat_visitors (
                  user_id           varchar(30)             NOT NULL    DEFAULT '',
                  user_type         varchar(12)             NOT NULL    DEFAULT '',
                  user_name         varchar(32)                         DEFAULT NULL,
                  user_ip           int(11)     unsigned                DEFAULT NULL,
                  user_email        varchar(90)                         DEFAULT NULL,
                  last_online       bigint(13)  unsigned                DEFAULT NULL,
                  UNIQUE KEY user_id (user_id)
                ) $collate;
            ";

		dbDelta( $ylc_tables );

		add_option( 'ylc_db_version', '' );

	}

}
