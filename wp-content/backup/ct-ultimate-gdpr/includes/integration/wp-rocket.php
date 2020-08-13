<?php

add_filter( 'rocket_htaccess_mod_rewrite', '__return_false' );
add_filter( 'rocket_cache_dynamic_cookies', 'ct_ultimate_gdpr_integration_wp_rocket_dynamic_cookies' );

/**
 * Add our cookies to the dynamic cookies list
 *
 * @since 2.7
 *
 * @param array $cookies Cookies to use for dynamic caching.
 *
 * @return array Updated cookies list
 */
function ct_ultimate_gdpr_integration_wp_rocket_dynamic_cookies( $cookies ) {
	$cookies[] = 'ct-ultimate-gdpr-cookie-level';
	$cookies[] = 'ct-ultimate-gdpr-terms-level';
	$cookies[] = 'ct-ultimate-gdpr-policy-level';

	return $cookies;
}

// update cache configuration file on plugin option save
add_action( 'update_option', 'ct_ultimate_gdpr_integration_wp_rocket_update_option', 10, 3 );
function ct_ultimate_gdpr_integration_wp_rocket_update_option( $option, $old_value, $value ) {
	if ( false === stripos( $option, 'ct-ultimate-gdpr' ) ) {
		return;
	}
	function_exists( 'rocket_generate_config_file' ) && rocket_generate_config_file();
}