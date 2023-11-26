<?php
/**
 * Functions for updating data, used by the background updater
 *
 * @package WC_Instagram/Functions
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Migrates the settings from older versions.
 */
function wc_instagram_update_200_migrate_settings() {
	// These settings are no longer valid.
	delete_option( 'woocommerce-instagram-settings' );

	// Enable the API changes notice.
	add_option( 'wc_instagram_display_api_changes_notice', 'yes' );
}

/**
 * Update DB Version.
 */
function wc_instagram_update_200_db_version() {
	WC_Instagram_Install::update_db_version( '2.0.0' );
}

/**
 * Renews the Instagram access credentials.
 */
function wc_instagram_update_210_renew_access() {
	wc_instagram_renew_access();
}

/**
 * Update DB Version.
 */
function wc_instagram_update_210_db_version() {
	WC_Instagram_Install::update_db_version( '2.1.0' );
}

/**
 * Renews the Instagram access credentials.
 */
function wc_instagram_update_220_renew_access() {
	wc_instagram_renew_access();
}

/**
 * Clears Instagram image transients.
 *
 * Forces a refresh of the images displayed on products pages.
 */
function wc_instagram_update_220_clear_image_transients() {
	wc_instagram_clear_product_hashtag_images_transients();
	wc_instagram_clear_hashtag_media_transients();
}

/**
 * Update DB Version.
 */
function wc_instagram_update_220_db_version() {
	WC_Instagram_Install::update_db_version( '2.2.0' );
}

/**
 * Updates the settings for the already created catalogs.
 */
function wc_instagram_update_320_update_catalogs() {
	$catalogs = get_option( 'wc_instagram_product_catalogs', array() );

	if ( empty( $catalogs ) ) {
		return;
	}

	foreach ( $catalogs as $index => $catalog ) {
		if ( isset( $catalog['product_images_option'] ) ) {
			continue;
		}

		// Keep backward compatibility.
		$catalog['product_images_option'] = 'featured';

		$catalogs[ $index ] = $catalog;
	}

	update_option( 'wc_instagram_product_catalogs', $catalogs );
}

/**
 * Update DB Version.
 */
function wc_instagram_update_320_db_version() {
	WC_Instagram_Install::update_db_version( '3.2.0' );
}

/**
 * Updates the settings for the already created catalogs.
 */
function wc_instagram_update_350_update_catalogs() {
	$catalogs = get_option( 'wc_instagram_product_catalogs', array() );

	if ( empty( $catalogs ) ) {
		return;
	}

	foreach ( $catalogs as $index => $catalog ) {
		if ( isset( $catalog['filter_by'], $catalog['products_option'] ) ) {
			continue;
		}

		// Keep backward compatibility.
		$catalog['filter_by']       = 'custom';
		$catalog['products_option'] = '';

		$catalogs[ $index ] = $catalog;
	}

	update_option( 'wc_instagram_product_catalogs', $catalogs );
}

/**
 * Update DB Version.
 */
function wc_instagram_update_350_db_version() {
	WC_Instagram_Install::update_db_version( '3.5.0' );
}

/**
 * Migrates the catalogs to the WP Posts table.
 */
function wc_instagram_update_400_migrate_catalogs() {
	$catalogs = get_option( 'wc_instagram_product_catalogs', array() );

	foreach ( $catalogs as $data ) {
		$catalog = wc_instagram_get_product_catalog( $data );

		if ( $catalog ) {
			$catalog->save();
		}
	}

	delete_option( 'wc_instagram_product_catalogs' );
}

/**
 * Update DB Version.
 */
function wc_instagram_update_400_db_version() {
	WC_Instagram_Install::update_db_version( '4.0.0' );
}
