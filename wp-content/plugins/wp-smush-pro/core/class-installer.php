<?php
/**
 * Smush installer (update/upgrade procedures): Installer class
 *
 * @package Smush\Core
 * @since 2.8.0
 *
 * @author Anton Vanyukov <anton@incsub.com>
 *
 * @copyright (c) 2018, Incsub (http://incsub.com)
 */

namespace Smush\Core;

use Smush\App\Abstract_Page;
use WP_Smush;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Installer for handling updates and upgrades of the plugin.
 *
 * @since 2.8.0
 */
class Installer {

	/**
	 * Triggered on Smush deactivation.
	 *
	 * @since 3.1.0
	 */
	public static function smush_deactivated() {
		if ( ! class_exists( '\\Smush\\Core\\Modules\\CDN' ) ) {
			require_once __DIR__ . '/modules/class-cdn.php';
		}

		Modules\CDN::unschedule_cron();
		Settings::get_instance()->delete_setting( 'wp-smush-cdn_status' );

		if ( is_multisite() && is_network_admin() ) {
			/**
			 * Updating the option instead of removing it.
			 *
			 * @see https://incsub.atlassian.net/browse/SMUSH-350
			 */
			update_site_option( 'wp-smush-networkwide', 1 );
		}

		delete_site_option( 'wp_smush_api_auth' );
	}

	/**
	 * Check if an existing install or new.
	 *
	 * @since 2.8.0  Moved to this class from wp-smush.php file.
	 */
	public static function smush_activated() {
		if ( ! defined( 'WP_SMUSH_ACTIVATING' ) ) {
			define( 'WP_SMUSH_ACTIVATING', true );
		}

		$version = get_site_option( 'wp-smush-version' );

		if ( ! class_exists( '\\Smush\\Core\\Settings' ) ) {
			require_once __DIR__ . '/class-settings.php';
		}

		Settings::get_instance()->init();
		$settings = Settings::get_instance()->get();

		// If the version is not saved or if the version is not same as the current version,.
		if ( ! $version || WP_SMUSH_VERSION !== $version ) {
			global $wpdb;
			// Check if there are any existing smush stats.
			$results = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT meta_id FROM {$wpdb->postmeta} WHERE meta_key=%s LIMIT 1",
					'wp-smpro-smush-data'
				)
			); // db call ok; no-cache ok.

			if ( $results || ( isset( $settings['auto'] ) && false !== $settings['auto'] ) ) {
				update_site_option( 'wp-smush-install-type', 'existing' );
			}

			// Create directory smush table.
			self::directory_smush_table();

			// Store the plugin version in db.
			update_site_option( 'wp-smush-version', WP_SMUSH_VERSION );
		}
	}

	/**
	 * Handle plugin upgrades.
	 *
	 * @since 2.8.0
	 */
	public static function upgrade_settings() {
		// Avoid executing this over an over in same thread.
		if ( defined( 'WP_SMUSH_ACTIVATING' ) || ( defined( 'WP_SMUSH_UPGRADING' ) && WP_SMUSH_UPGRADING ) ) {
			return;
		}

		$version = get_site_option( 'wp-smush-version' );

		if ( false === $version ) {
			self::smush_activated();
		}

		if ( false !== $version && WP_SMUSH_VERSION !== $version ) {
			if ( ! defined( 'WP_SMUSH_UPGRADING' ) ) {
				define( 'WP_SMUSH_UPGRADING', true );
			}

			if ( version_compare( $version, '3.7.0', '<' ) ) {
				self::upgrade_3_7_0();
			}

			if ( version_compare( $version, '3.8.0', '<' ) ) {
				// Delete the flag for hiding the BF modal because it was removed.
				delete_site_option( 'wp-smush-hide_blackfriday_modal' );
			}

			if ( version_compare( $version, '3.8.3', '<' ) ) {
				// Delete this unused setting, leftover from old smush.
				delete_option( 'wp-smush-transparent_png' );
			}

			if ( version_compare( $version, '3.8.6', '<' ) ) {
				add_site_option( 'wp-smush-show_upgrade_modal', true );
			}

			if ( version_compare( $version, '3.9.0', '<' ) ) {
				// Hide the Local WebP wizard if Local WebP is enabled.
				if ( Settings::get_instance()->get( 'webp_mod' ) ) {
					add_site_option( 'wp-smush-webp_hide_wizard', true );
				}
			}

			if ( version_compare( $version, '3.9.1', '<' ) ) {
				// Add the flag to display the release highlights modal.
				add_site_option( 'wp-smush-show_upgrade_modal', true );
			}

			if ( version_compare( $version, '3.9.5', '<' ) ) {
				delete_site_option( 'wp-smush-show-black-friday' );
			}

			if ( version_compare( $version, '3.9.10', '<' ) ) {
				self::dir_smush_set_primary_key();
			}

			if ( version_compare( $version, '3.10.0', '<' ) ) {
				self::upgrade_3_10_0();
			}

			if ( version_compare( $version, '3.10.3', '<' ) ) {
				self::upgrade_3_10_3();
			}

			$hide_new_feature_highlight_modal = apply_filters( 'wpmudev_branding_hide_doc_link', false );
			if ( ! $hide_new_feature_highlight_modal && version_compare( $version, '3.12.0', '<' ) ) {
				// Add the flag to display the new feature background process modal.
				add_site_option( 'wp-smush-show_upgrade_modal', true );
			}

			// Create/upgrade directory smush table.
			self::directory_smush_table();

			// Store the latest plugin version in db.
			update_site_option( 'wp-smush-version', WP_SMUSH_VERSION );
		}
	}

	/**
	 * Create or upgrade custom table for directory Smush.
	 *
	 * After creating or upgrading the custom table, update the path_hash
	 * column value and structure if upgrading from old version.
	 *
	 * @since 2.9.0
	 */
	public static function directory_smush_table() {
		if ( ! class_exists( '\\Smush\\Core\\Modules\\Abstract_Module' ) ) {
			require_once __DIR__ . '/modules/class-abstract-module.php';
		}

		if ( ! class_exists( '\\Smush\\Core\\Modules\\Dir' ) ) {
			require_once __DIR__ . '/modules/class-dir.php';
		}

		// No need to continue on sub sites.
		if ( ! Modules\Dir::should_continue() ) {
			return;
		}

		// Create a class object, if doesn't exists.
		if ( ! is_object( WP_Smush::get_instance()->core()->mod->dir ) ) {
			WP_Smush::get_instance()->core()->mod->dir = new Modules\Dir();
		}

		// Create/upgrade directory smush table.
		WP_Smush::get_instance()->core()->mod->dir->create_table();
	}

	/**
	 * Set primary key for directory smush table on upgrade to 3.9.10.
	 *
	 * @since 3.9.10
	 */
	private static function dir_smush_set_primary_key() {
		global $wpdb;

		// Only call it after creating table smush_dir_images. If the table doesn't exist, returns.
		if ( ! Modules\Dir::table_exist() ) {
			return;
		}

		// If the table is already set the primary key, return.
		if ( $wpdb->query( $wpdb->prepare( "SHOW INDEXES FROM {$wpdb->base_prefix}smush_dir_images WHERE Key_name = %s;", 'PRIMARY' ) ) ) {
			return;
		}

		// Set column ID as a primary key.
		$wpdb->query( "ALTER TABLE {$wpdb->base_prefix}smush_dir_images ADD PRIMARY KEY (id);" );
	}

	/**
	 * Check if table needs to be created and create if not exists.
	 *
	 * @since 3.8.6
	 */
	public static function maybe_create_table() {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}

		if ( isset( get_current_screen()->id ) && false === strpos( get_current_screen()->id, 'page_smush' ) ) {
			return;
		}

		self::directory_smush_table();
	}

	/**
	 * Upgrade to 3.7.0
	 *
	 * @since 3.7.0
	 */
	private static function upgrade_3_7_0() {
		delete_site_option( 'wp-smush-run_recheck' );

		// Fix the "None" animation in lazy-load options.
		$lazy = Settings::get_instance()->get_setting( 'wp-smush-lazy_load' );

		if ( ! $lazy || ! isset( $lazy['animation'] ) || ! isset( $lazy['animation']['selected'] ) ) {
			return;
		}

		if ( '0' === $lazy['animation']['selected'] ) {
			$lazy['animation']['selected'] = 'none';
			Settings::get_instance()->set_setting( 'wp-smush-lazy_load', $lazy );
		}
	}

	/**
	 * Upgrade to 3.10.0
	 *
	 * @since 3.10.0
	 *
	 * @return void
	 */
	private static function upgrade_3_10_0() {
		// Remove unused options.
		delete_site_option( 'wp-smush-hide_pagespeed_suggestion' );
		delete_site_option( 'wp-smush-hide_upgrade_notice' );

		// Rename the default config.
		$stored_configs = get_site_option( 'wp-smush-preset_configs', false );
		if ( is_array( $stored_configs ) && isset( $stored_configs[0] ) && isset( $stored_configs[0]['name'] ) && 'Basic config' === $stored_configs[0]['name'] ) {
			$stored_configs[0]['name'] = __( 'Default config', 'wp-smushit' );
			update_site_option( 'wp-smush-preset_configs', $stored_configs );
		}

		// Show new features modal for free users.
		if ( ! WP_Smush::is_pro() ) {
			if ( is_multisite() && ! Abstract_Page::should_render( 'bulk' ) ) {
				return;
			}

			add_site_option( 'wp-smush-show_upgrade_modal', true );
		}
	}

	/**
	 * Upgrade 3.10.3
	 *
	 * @since 3.10.3
	 *
	 * @return void
	 */
	private static function upgrade_3_10_3() {
		delete_site_option( 'wp-smush-hide_smush_welcome' );
		// Logger options.
		delete_site_option( 'wdev_logger_wp-smush-pro' );
		delete_site_option( 'wdev_logger_wp-smushit' );
		// Clean old cronjob (missing callback).
		if ( wp_next_scheduled( 'wdev_logger_clear_logs' ) ) {
			wp_clear_scheduled_hook( 'wdev_logger_clear_logs' );
		}
	}
}
