<?php
/**
 * This file is loaded early before the plugin activation hook.
 *
 * This file will be included/executed in Core::init_early().
 */

/**
 * Pro plugin activation hook.
 * The lower priority (20) will allow the lite version (Core) to perform its activation steps first.
 */
add_action(
	'activate_' . plugin_basename( WPMS_PLUGIN_FILE ),
	function () {

		/**
		 * Force Lite languages download.
		 *
		 * This section will force to download any new translations for Lite version
		 * right away instead of waiting for up to 12 hours.
		 */
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/class-automatic-upgrader-skin.php';

		$locales = array_unique( [ get_locale(), get_user_locale() ] );

		if ( count( $locales ) === 1 && $locales[0] === 'en_US' ) {
			return;
		}

		$to_update = [];

		foreach ( $locales as $locale ) {
			$to_update[] = (object) [
				'type'       => 'plugin',
				'slug'       => 'wp-mail-smtp',
				'language'   => $locale,
				'version'    => WPMS_PLUGIN_VER,
				'package'    => 'https://downloads.wordpress.org/translation/plugin/wp-mail-smtp/' . WPMS_PLUGIN_VER . '/' . $locale . '.zip',
				'autoupdate' => true,
			];
		}

		$upgrader = new Language_Pack_Upgrader( new Automatic_Upgrader_Skin() );

		$upgrader->bulk_upgrade( $to_update );
	},
	20
);

/**
 * Set default settings for Pro plugin.
 *
 * We need to apply this filter before the Lite activation hook
 * and that's why it should be located here.
 *
 * @since 3.11.0
 */
add_filter(
	'wp_mail_smtp_options_get_defaults',
	function ( $defaults ) {
		return array_merge(
			$defaults,
			[
				'gmail' => [
					'one_click_setup_enabled' => true,
				],
			]
		);
	}
);
