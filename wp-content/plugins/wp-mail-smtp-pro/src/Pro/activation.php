<?php
/**
 * Pro plugin activation hook.
 * The lower priority (20) will allow the lite version (Core) to perform its activation steps first.
 *
 * This file will be included/executed in Core::init_early().
 */

add_action( 'activate_' . plugin_basename( WPMS_PLUGIN_FILE ), function () {

	/**
	 * Force Lite languages download.
	 *
	 * This section will force to download any new translations for Lite version
	 * right away instead of waiting for up to 12 hours.
	 */
	include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
	require_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

	$locales = array_unique( array( get_locale(), get_user_locale() ) );

	if ( 1 === count( $locales ) && 'en_US' === $locales[0] ) {
		return;
	}

	$to_update = array();

	foreach ( $locales as $locale ) {
		$to_update[] = (object) array(
			'type'       => 'plugin',
			'slug'       => 'wp-mail-smtp',
			'language'   => $locale,
			'version'    => WPMS_PLUGIN_VER,
			'package'    => 'https://downloads.wordpress.org/translation/plugin/wp-mail-smtp/' . WPMS_PLUGIN_VER . '/' . $locale . '.zip',
			'autoupdate' => true,
		);
	}

	$upgrader = new \Language_Pack_Upgrader( new \Automatic_Upgrader_Skin() );
	$upgrader->bulk_upgrade( $to_update );
}, 20 );
