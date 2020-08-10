<?php

/**
* EnergyPlus
*
* EnergyPlus is a hybrid panel for your store!
*
* @link              https://en.er.gy/plus
* @since             1.0.0
* @package           EnergyPlus
*
* @wordpress-plugin
* Plugin Name:       Energy+
* Plugin URI:        https://en.er.gy/docs/plus?v=1.2.2
* Description:       Energy+ is a beautiful admin panel for Woocommerce.
* Version:           1.2.2
* Author:            EN.ER.GY
* Author URI:        https://en.er.gy
* Text Domain:       energyplus
* Domain Path:       /framework/languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
* Current plugin version and paths.
*/


	if (!defined( 'EnergyPlus_Version' )) {
		define( 'EnergyPlus_Version',   '1.2.2' );
	}

	define( 'EnergyPlus_Public',    plugin_dir_url( __FILE__ ) . 'public/' );
	define( 'EnergyPlus_Framework', plugin_dir_path( __FILE__ ) . 'framework/' );
	define( 'EnergyPlus_Dir',       plugin_dir_path( __FILE__ ) );


	/**
	* Activate EnergyPlus
	*
	* @since  1.0
	* @return void
	*/

	function activate_energyplus() {
		require_once plugin_dir_path( __FILE__ ) . 'framework/libs/core/energyplus-activator.php';
		EnergyPlus_Activator::activate();
	}

	/**
	* Deactivate EnergyPlus
	*
	* @since  1.0
	* @return void
	*/

	function deactivate_energyplus() {
		require_once plugin_dir_path( __FILE__ ) . 'framework/libs/core/energyplus-deactivator.php';
		EnergyPlus_Deactivator::deactivate();
	}

	/**
	* Activation/Deactivation hooks
	*/

	register_activation_hook( __FILE__, 'activate_energyplus' );
	register_deactivation_hook( __FILE__, 'deactivate_energyplus' );


	/**
	* Safe Mode for EnergyPlus
	*/

	add_action('init', 'energyplus_init');

	function energyplus_init() {


		if ( (isset($_GET['page']) && 'energyplus' === $_GET['page']) && (isset($_GET['segment']) && 'safe-mode' === $_GET['segment']) ) {

			if ((isset($_GET['page']) && 'energyplus' === $_GET['page'] && 'on' === $_GET['action']) ) {

				if (wp_verify_nonce($_REQUEST['_wpnonce'], 'energyplus-safe-mode')) {
					$plugins = array(
						'energy-plus/energy-plus.php'
					);

					require_once(ABSPATH . 'wp-admin/includes/plugin.php');

					deactivate_plugins($plugins);

					wp_die(sprintf(
						__('<h1>Energy+ is deactivated</h1><br><br><a href="%s">Return to Wordpress Admin</a>', 'energyplus'), admin_url('plugins.php')
					));
				}
			}

			$nonce = wp_create_nonce('energyplus-safe-mode');

			wp_die(sprintf(
				__('<h1>Energy+ Safe Mode</h1><br>If Energy+ is not working properly and causes errors on your site, you can temporarily deactivate Energy+.<br><br><a href="%s">Deactivate Energy+ now</a>', 'energyplus'), admin_url('admin.php?page=energyplus&segment=safe-mode&action=on&_wpnonce='.$nonce)
			));
		}
	}

	/**
	* SPL Autoload
	*
	* @since    1.0.0
	*/
	spl_autoload_register(function ($class) {

		if( class_exists( $class )) return;

		$file = str_replace("_","-", strtolower($class)) . '.php';
		$file = sanitize_file_name($file);
		$file = EnergyPlus_Framework. 'controller/' . $file;

		if (file_exists($file)) {
			require $file;
		} else {

			$file = str_replace(array( "__", "_"), array("/", "-"), strtolower($class)) . '.php';
			$file = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $file));
			$file = EnergyPlus_Framework.  'libs/' . $file;
			if (file_exists($file)) {
				require $file;
			}
		}
	}
);

/**
* Let's start.
*
* @since    1.0.0
*/

add_action( 'plugins_loaded', 'energyplus_plugins_loaded' );

function energyplus_plugins_loaded() {
	if (class_exists('WooCommerce', false)) {
		require EnergyPlus_Framework. 'controller/energyplus.php';

		(new EnergyPlus())->start();
	}
}
