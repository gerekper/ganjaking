<?php

/**
* EnergyPlus Uninstall
*
* Fired during EnergyPlus uninstall
*
* @since      1.0.0
* @package    EnergyPlus
* @subpackage EnergyPlus/framework
* @author     EN.ER.GY <support@en.er.gy>
* */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove options
$options = array(
	'menu',
	'mode-energyplus-orders',
	'mode-energyplus-products',
	'mode-energyplus-customers',
	'mode-energyplus-coupons',
	'mode-energyplus-comments',
	'feature-full',
	'feature-auto',
	'feature-use-administrator',
	'feature-use-shop_manager',
	'feature-logo',
	'feature-pulse',
	'feature-own_themes',
	'feature-sounds_notification',
	'feature-sounds_product',
	'feature-sounds_checkout',
	'goals-daily',
	'goals-weekly',
	'goals-monthly',
	'goals-yearly',
	'refresh',
	'dashboard_widgets',
	'dashboard_widgets_settings',
	'reactors_list',
	'reactors-tweaks-window-size',
	'reactors-tweaks-window-size-dimension',
	'reactors-tweaks-order-statuses',
	'reactors-tweaks-adminbar-hotkey',
	'reactors-tweaks-landing',
	'reactors-tweaks-settings-woocommerce',
	'reactors-tweaks-icon-text',
	'reactors-tweaks-font',
	'reactors-tweaks-screenoptions',
	'reactors-tweaks-order-cond',
	'reactors-login-settings'
);

foreach ($options AS $option) {
	delete_option('energyplus_' . $option);
}

// Remove tables
global $wpdb;

$tableArray = array(
	$wpdb->prefix . "energyplus_daily",
	$wpdb->prefix . "energyplus_events",
	$wpdb->prefix . "energyplus_requests",
);

foreach ($tableArray as $tablename) {
	$wpdb->query("DROP TABLE IF EXISTS $tablename");
}
