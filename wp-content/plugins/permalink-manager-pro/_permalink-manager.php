<?php

/**
 * Load 'permalink-manager-pro/permalink-manager-pro.php' instead of 'permalink-manager-pro/permalink-manager.php' file
 */
if(!function_exists('activate_plugin')) {
	require_once(ABSPATH . '/wp-admin/includes/plugin.php');
}

/**
 * Activate Permalink Manager Pro
 */
activate_plugin('permalink-manager-pro/permalink-manager-pro.php');
