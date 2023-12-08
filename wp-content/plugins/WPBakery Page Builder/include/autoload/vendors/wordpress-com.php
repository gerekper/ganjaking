<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @since 4.4 vendors initialization moved to hooks in autoload/vendors.
 *
 * Used to initialize wordpress.com vendor.
 */
add_action(
	'wpcom_marketplace_webhook_response_js-composer',
	'vc_init_vendor_wordpress_com',
	10,
	3
);

/**
 * Activate license for wordpress.com
 *
 * @param bool $result
 * @param array $license_payload
 * @param string $event_type
 */
function vc_init_vendor_wordpress_com( $result, $license_payload, $event_type ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require class-vc-wxr-parser-plugin.php to use is_plugin_active() below
	require_once vc_path_dir( 'SETTINGS_DIR', 'class-vc-license.php' );

	if ( 'provision_license' !== $event_type ) {
		return;
	}

	if ( empty( $license_payload['license'] ) ) {
		return;
	}

	$license = new Vc_License();

	$license->setLicenseOptions( $license_payload['license'] );
}
