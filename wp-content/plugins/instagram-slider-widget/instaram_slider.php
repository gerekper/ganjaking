<?php
/*
Plugin Name: Social Slider Widget
Plugin URI: https://cm-wp.com/instagram-slider-widget
Version: 1.9.4
Description: Social Slider Widget is a responsive slider widget that shows 12 latest images from a public Instagram user and up to 18 images from a hashtag and Youtube videos
Author: creativemotion
Author URI: https://cm-wp.com/
Text Domain: instagram-slider-widget
Domain Path: /languages
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Подключаем класс проверки совместимости
require_once( dirname( __FILE__ ) . '/libs/factory/core/includes/class-factory-requirements.php' );
require_once( dirname( __FILE__ ) . '/vendor/autoload.php');

$plugin_info = array(
	'prefix'               => 'wis_',
	'plugin_name'          => 'wisw',
	'plugin_title'         => __( 'Social Slider Widget', 'instagram-slider-widget' ),
	'plugin_text_domain'   => 'instagram-slider-widget',

	// Служба поддержки
	'support_details'      => array(
		'url'       => 'https://cm-wp.com/instagram-slider-widget',// Ссылка на сайт плагина
		'pages_map' => array(
			'features' => 'premium-features', // {site}/premium-features "страница возможности"
			'pricing'  => 'pricing', // {site}/prices страница "цены"
			'support'  => 'support', // {site}/support страница "служба поддержки"
			'docs'     => 'docs' // {site}/docs страница "документация"
		)
	),

	// Настройка обновлений плагина
	'has_updates'          => true,
	'updates_settings'     => array(
		'repository'        => 'wordpress',
		'slug'              => 'instagram-slider-widget',
		'maybe_rollback'    => true,
		'rollback_settings' => array(
			'prev_stable_version' => '0.0.0'
		)
	),

	// Настройка премиум плагина
	'has_premium'          => true,
	'license_settings'     => array(
		'has_updates'      => true,
		'provider'         => 'freemius',
		'slug'             => 'instagram-slider-widget-premium',
		'plugin_id'        => '4272',
		'public_key'       => 'pk_5152229a4aba03187267a8bc88874',
		'price'            => 39,
		'updates_settings' => array(
			'maybe_rollback'    => true, // Можно ли делать откат к предыдущей версии плагина?
			'rollback_settings' => array(
				'prev_stable_version' => '0.0.0'
			)
		)
	),

	// Настройки рекламы от CreativeMotion
	'render_adverts'       => true,
	'adverts_settings'     => array(
		'dashboard_widget' => true,
		'right_sidebar'    => true,
		'notice'           => true,
	),

	// PLUGIN SUBSCRIBE FORM
	'subscribe_widget'     => true,
	'subscribe_settings'   => [ 'group_id' => '105407119' ],

	'load_factory_modules' => array(
		array( 'libs/factory/bootstrap', 'factory_bootstrap_445', 'admin' ),
		array( 'libs/factory/forms', 'factory_forms_442', 'admin' ),
		array( 'libs/factory/pages', 'factory_pages_444', 'admin' ),
		array( 'libs/factory/freemius', 'factory_freemius_133', 'all' ),
		array( 'libs/factory/adverts', 'factory_adverts_123', 'admin' ),
		array( 'libs/factory/clearfy', 'factory_clearfy_236', 'admin' ),
	)
);

$wis_compatibility = new Wbcr_Factory445_Requirements( __FILE__, array_merge( $plugin_info, array(
	'plugin_already_activate'          => defined( 'WIS_PLUGIN_ACTIVE' ),
	'required_php_version'             => '7.0',
	'required_wp_version'              => '4.8.0',
	'required_clearfy_check_component' => false
) ) );

/**
 * If the plugin is compatible, then it will continue its work, otherwise it will be stopped,
 * and the user will throw a warning.
 */
if ( ! $wis_compatibility->check() ) {
	return;
}
/********************************************/
// Устанавливает статус плагина, как активный
define( 'WIS_PLUGIN_ACTIVE', true );
// Версия плагина
define( 'WIS_PLUGIN_VERSION', $wis_compatibility->get_plugin_version() );
define( 'WIS_PLUGIN_FILE', __FILE__ );
define( 'WIS_ABSPATH', dirname( __FILE__ ) );
define( 'WIS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WIS_PLUGIN_SLUG', dirname( plugin_basename( __FILE__ ) ) );
// Ссылка к директории плагина
define( 'WIS_PLUGIN_URL', plugins_url( null, __FILE__ ) );
// Директория плагина
define( 'WIS_PLUGIN_DIR', dirname( __FILE__ ) );
/********************************************/


/**************БЛОК ЮТУБА*************/
// Устанавливает статус плагина, как активный
define( 'WYT_PLUGIN_ACTIVE', true );
// Версия плагина
define( 'WYT_PLUGIN_FILE', __FILE__ . '/components/youtube' );
define( 'WYT_PLUGIN_VERSION',  $wis_compatibility->get_plugin_version() );
define( 'WYT_ABSPATH', dirname( __FILE__ ) . '/components/youtube'  );
define( 'WYT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) . '/components/youtube'  );
define( 'WYT_PLUGIN_SLUG', dirname( plugin_basename( __FILE__) . '/components/youtube' ) );
// Ссылка к директории плагина
define( 'WYT_PLUGIN_URL', plugins_url( null, __FILE__) . '/components/youtube' );
// Директория плагина
define( 'WYT_PLUGIN_DIR', dirname( __FILE__ ) . '/components/youtube' );

/*
 * Константа определяет какое имя опции для хранения данных.
 * Нужно для отладки и последующего бесшовного перехода
 */
define( 'WYT_ACCOUNT_OPTION_NAME', 'account' );
define( 'WYT_API_KEY_OPTION_NAME', 'yt_api_key' );
/***************************************************/

require_once WYT_PLUGIN_DIR . '/includes/helpers.php';



/**
 * -----------------------------------------------------------------------------
 * PLUGIN INIT
 * -----------------------------------------------------------------------------
 */
require_once( WIS_PLUGIN_DIR . '/libs/factory/core/boot.php' );
require_once( WIS_PLUGIN_DIR . '/includes/class-wis-plugin.php' );

try {
	require_once (WIS_PLUGIN_DIR . '/components/youtube/includes/Api/load.php');
	new \Instagram\Includes\WIS_Plugin( __FILE__, array_merge( $plugin_info, array(
		'plugin_version' => WIS_PLUGIN_VERSION
	) ) );
} catch ( Exception $e ) {
	// Plugin wasn't initialized due to an error
	define( 'WIS_PLUGIN_THROW_ERROR', true );

	$wis_plugin_error_func = function () use ( $e ) {
		$error = sprintf( "The %s plugin has stopped. <b>Error:</b> %s Code: %s", 'Social Slider Widget', $e->getMessage(), $e->getCode() );
		echo '<div class="notice notice-error"><p>' . $error . '</p></div>';
	};

	add_action( 'admin_notices', $wis_plugin_error_func );
	add_action( 'network_admin_notices', $wis_plugin_error_func );
}

define( 'WIS_INSTAGRAM_CLIENT_ID', '2555361627845349' );
define( 'WIS_FACEBOOK_CLIENT_ID', '776212986124330' );
//define( 'WIS_FACEBOOK_CLIENT_ID', '572623036624544' );

/*
 * Константа определяет какое имя опции для хранения данных.
 * Нужно для отладки и последующего бесшовного перехода
 */
define( 'WIS_ACCOUNT_PROFILES_OPTION_NAME', 'account_profiles' );
define( 'WIS_ACCOUNT_PROFILES_NEW_OPTION_NAME', 'account_profiles_new' );

define( 'WIS_FACEBOOK_ACCOUNT_PROFILES_OPTION_NAME', 'facebook_account_profiles' );

/*******************************************************************************/
/**
 * On widgets Init register Widget
 */
require_once WIS_PLUGIN_DIR . '/includes/class.wis_social.php';

require_once WIS_PLUGIN_DIR . "/includes/class-wis_instagram_slider.php";
require_once WIS_PLUGIN_DIR . "/components/youtube/includes/class-youtube-widget.php";
add_action( 'plugins_loaded', function () {
	add_action( 'widgets_init', array( 'WIS_InstagramSlider', 'register_widget' ) );
	add_action( 'widgets_init', array( 'WYT_Widget', 'register_widget' ) );
} );

//require_once WIS_PLUGIN_DIR."/includes/class-wis_facebook_slider.php";
//add_action( 'widgets_init', array( 'WIS_FacebookSlider', 'register_widget' ) );
?>
