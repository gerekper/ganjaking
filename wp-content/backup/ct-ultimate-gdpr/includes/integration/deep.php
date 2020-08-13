<?php

add_filter( 'ct_ultimate_gdpr_op_load', 'ct_ultimate_gdpr_integration_deep_disable_op' );
/**
 * @param $is_enabled
 *
 * @return bool
 */
function ct_ultimate_gdpr_integration_deep_disable_op( $is_enabled ) {

	if ( false !== stripos( $_SERVER['REQUEST_URI'], 'wn-admin-plugins' ) ) {
		$is_enabled = false;
	}

	return $is_enabled;
}

if ( ct_ultimate_gdpr_get_value( 'ct-tgmpa', $_GET ) || ct_ultimate_gdpr_get_value( 'plugin', $_GET ) == 'ct-ultimate-gdpr' ) {
	add_action( 'tgmpa_register', 'ct_ultimate_gdpr_integration_deep_disable_tgmpa', 5 );
}

/**
 *
 */
function ct_ultimate_gdpr_integration_deep_disable_tgmpa() {
	remove_action( 'tgmpa_register', 'deep_register_required_plugins' );
	remove_filter( 'site_transient_update_plugins', 'wn_plugin_updates' );
	remove_all_filters( 'upgrader_post_install' );
}