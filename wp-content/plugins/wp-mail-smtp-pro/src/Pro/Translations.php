<?php

namespace WPMailSMTP\Pro;

use stdClass;
use Language_Pack_Upgrader;
use Automatic_Upgrader_Skin;

/**
 * Translations class for downloading new Pro plugin language strings.
 *
 * @since 3.9.0
 */
class Translations {

	/**
	 * Plugin slug.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	private $plugin_slug = 'wp-mail-smtp-pro';

	/**
	 * List of installed translations.
	 *
	 * @since 3.9.0
	 *
	 * @var array
	 */
	private $installed_translations = [];

	/**
	 * List of available languages.
	 *
	 * @since 3.9.0
	 *
	 * @var array
	 */
	private $available_languages = [];

	/**
	 * Full URL for the plugin handled by our redirection at WPMailSMTP.com.
	 *
	 * @since 3.9.0
	 */
	const API_URL = 'https://translations.wpmailsmtp.com/%s/packages.json';

	/**
	 * The instance of the core class used for updating/installing language packs (translations).
	 *
	 * @since 3.9.0
	 *
	 * @var Language_Pack_Upgrader
	 */
	private $upgrader;

	/**
	 * Upgrader Skin for Automatic WordPress Upgrades.
	 *
	 * @since 3.9.0
	 *
	 * @var Automatic_Upgrader_Skin
	 */
	private $skin;

	/**
	 * Whether the class should be loaded.
	 *
	 * @since 3.9.0
	 *
	 * @return bool
	 */
	private function allow_load() {

		if ( ! is_admin() ) {
			return false;
		}

		// For WordPress versions 4.9.0-4.9.4 this file must be included before the current_user_can() check.
		require_once ABSPATH . 'wp-admin/includes/template.php';

		if ( ! current_user_can( 'install_languages' ) ) {
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/translation-install.php';

		return wp_can_install_language_pack();
	}

	/**
	 * Load the functionality via hooks.
	 *
	 * @since 3.9.0
	 */
	public function hooks() {

		global $pagenow;

		// Exit early if the functionality should not be loaded.
		if ( ! $this->allow_load() ) {
			return;
		}

		if ( $pagenow === 'update-core.php' ) {

			// Clear cache for translations.
			add_action( 'set_site_transient_update_plugins', [ $this, 'clear_translations_cache' ] );

			// Add translations to the list of available for download.
			add_filter( 'site_transient_update_plugins', [ $this, 'register_t15s_translations' ] );
		}

		// Download translations on plugin activation.
		add_action( 'activate_plugin', [ $this, 'activate_plugin' ] );

		// Remove translation cache for a plugin on deactivation.
		// Translation removal is handled on plugin removal by WordPress.
		add_action( 'deactivate_plugin', [ $this, 'clear_plugin_translation_cache' ] );

		// Download translations when language for the site has been changed.
		add_action( 'update_option_WPLANG', [ $this, 'download_plugins_translations' ] );

		// Download translations on plugin activation on Plugins page.
		if (
			$pagenow === 'plugins.php' &&
			get_transient( 'wp_mail_smtp_just_activated' ) &&
			( ! empty( $_GET['activate'] ) || ! empty( $_GET['activate-multi'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		) {
			$this->download_plugins_translations();
		}
	}

	/**
	 * Whether the provided slug is WP Mail SMTP Pro plugin.
	 *
	 * @since 3.9.0
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return bool
	 */
	private function is_wpmailsmtp_pro_plugin( $slug ) {

		return strpos( $slug, 'wp-mail-smtp-pro' ) === 0;
	}

	/**
	 * Get WP Mail SMTP Pro plugin slug.
	 *
	 * @since 3.9.0
	 *
	 * @return string
	 */
	private function get_plugin_slug() {

		return $this->plugin_slug;
	}

	/**
	 * Get available translations for the plugin.
	 *
	 * @since 3.9.0
	 *
	 * @param array  $translations List of translations.
	 * @param string $slug         Plugin slug.
	 *
	 * @return array
	 */
	private function get_available_plugin_translations( $translations, $slug ) {

		$available_languages = $this->get_available_languages();

		if ( empty( $available_languages ) ) {
			return [];
		}

		foreach ( $translations as $key => $language ) {
			if ( ! is_object( $language ) ) {
				$language = (object) $language;
			}
			if (
				( ! property_exists( $language, 'slug' ) || ! property_exists( $language, 'language' ) ) ||
				$slug !== $language->slug ||
				! in_array( $language->language, $available_languages, true )
			) {
				unset( $translations[ $key ] );
			}
		}

		return $translations;
	}

	/**
	 * Download translations for the plugin.
	 *
	 * @since 3.9.0
	 *
	 * @param string $slug         Slug of plugin.
	 * @param array  $translations List of available translations.
	 */
	private function download_plugin_translations( $slug, $translations ) {

		$this->load_download_requirements();

		$available_translations = $this->get_available_plugin_translations( $translations, $slug );

		foreach ( $available_translations as $language ) {
			if ( ! is_object( $language ) ) {
				$language = (object) $language;
			}

			$this->skin->language_update = $language;

			$this->upgrader->run(
				[
					'package'                     => $language->package,
					'destination'                 => WP_LANG_DIR . '/plugins',
					'abort_if_destination_exists' => false,
					'is_multi'                    => true,
					'hook_extra'                  => [
						'language_update_type' => $language->type,
						'language_update'      => $language,
					],
				]
			);
		}
	}

	/**
	 * Load required libraries.
	 *
	 * @since 3.9.0
	 */
	private function load_download_requirements() {

		$this->skin     = new Automatic_Upgrader_Skin();
		$this->upgrader = new Language_Pack_Upgrader( $this->skin );
	}

	/**
	 * Download translations.
	 *
	 * @since 3.9.0
	 */
	public function download_plugins_translations() {

		$slug         = $this->get_plugin_slug();
		$translations = $this->get_translations( $slug );

		if ( ! empty( $translations['translations'] ) ) {
			$this->download_plugin_translations( $slug, $translations['translations'] );
		}
	}

	/**
	 * Get all available translations for the plugin.
	 *
	 * @since 3.9.0
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return array Translation data.
	 */
	private function get_available_translations( $slug ) {

		$translations = get_site_transient( $this->get_cache_key( $slug ) );

		if ( $translations !== false ) {
			return $translations;
		}

		$translations = json_decode(
			wp_remote_retrieve_body(
				wp_remote_get(
					sprintf( self::API_URL, $slug ),
					[
						'timeout' => 2,
					]
				)
			),
			true
		);

		if ( ! is_array( $translations ) || empty( $translations['translations'] ) ) {
			$translations = [ 'translations' => [] ];
		}

		// Convert translations from API to a WordPress standard.
		foreach ( $translations['translations'] as $key => $translation ) {
			$translations['translations'][ $key ]['type'] = 'plugin';
			$translations['translations'][ $key ]['slug'] = $slug;
		}

		set_site_transient( $this->get_cache_key( $slug ), $translations );

		return $translations;
	}

	/**
	 * Get a list of needed translations for the plugin.
	 *
	 * @since 3.9.0
	 *
	 * @param string $slug Plugin slug.
	 *
	 * @return array
	 */
	private function get_translations( $slug ) {

		$translations           = $this->get_available_translations( $slug );
		$available_languages    = $this->get_available_languages();
		$installed_translations = $this->get_installed_translations();

		foreach ( $translations['translations'] as $key => $translation ) {
			if ( empty( $translation['language'] ) || ! in_array( $translation['language'], $available_languages, true ) ) {
				unset( $translations['translations'][ $key ] );
			}

			// Skip languages which were updated locally.
			if ( isset( $installed_translations[ $slug ][ $translation['language'] ]['PO-Revision-Date'], $translation['updated'] ) ) {
				$local  = strtotime( $installed_translations[ $slug ][ $translation['language'] ]['PO-Revision-Date'] );
				$remote = strtotime( $translation['updated'] );

				if ( $local >= $remote ) {
					unset( $translations['translations'][ $key ] );
				}
			}
		}

		return $translations;
	}

	/**
	 * Register all translations from our Translations endpoint.
	 *
	 * @since 3.9.0
	 *
	 * @param object $value Value of the `update_plugins` transient option.
	 *
	 * @return stdClass
	 */
	public function register_t15s_translations( $value ) {

		if ( ! $value ) {
			$value = new stdClass();
		}

		if ( ! isset( $value->translations ) ) {
			$value->translations = [];
		}

		$slug         = $this->get_plugin_slug();
		$translations = $this->get_translations( $slug );

		if ( empty( $translations['translations'] ) ) {
			return $value;
		}

		foreach ( $translations['translations'] as $translation ) {
			$value->translations[] = $translation;
		}

		return $value;
	}

	/**
	 * Get a dynamic cache key which has the plugin slug in its name.
	 *
	 * @since 3.9.0
	 *
	 * @param string $slug Slug.
	 *
	 * @return string
	 */
	private function get_cache_key( $slug ) {

		return "wp_mail_smtp_t15s_{$slug}";
	}

	/**
	 * Clear existing translation cache.
	 *
	 * @since 3.9.0
	 */
	public function clear_translations_cache() {

		delete_site_transient( $this->get_cache_key( $this->get_plugin_slug() ) );
	}

	/**
	 * Clear existing translation cache for a specific plugin.
	 *
	 * @since 3.9.0
	 *
	 * @param string $plugin Plugin slug.
	 */
	public function clear_plugin_translation_cache( $plugin ) {

		$slug = dirname( $plugin );

		if ( ! $this->is_wpmailsmtp_pro_plugin( $slug ) ) {
			return;
		}

		delete_site_transient( $this->get_cache_key( $slug ) );
	}

	/**
	 * Get available languages.
	 *
	 * @since 3.9.0
	 *
	 * @return array
	 */
	private function get_available_languages() {

		if ( $this->available_languages ) {
			return $this->available_languages;
		}

		$this->available_languages = get_available_languages();

		return $this->available_languages;
	}

	/**
	 * Get installed translations.
	 *
	 * @since 3.9.0
	 *
	 * @return array
	 */
	private function get_installed_translations() {

		if ( $this->installed_translations ) {
			return $this->installed_translations;
		}

		$this->installed_translations = wp_get_installed_translations( 'plugins' );

		return $this->installed_translations;
	}

	/**
	 * Download translations for the plugin after its activation.
	 *
	 * @since 3.9.0
	 *
	 * @param string $plugin Plugin main file.
	 */
	public function activate_plugin( $plugin ) {

		$slug = dirname( $plugin );

		if ( ! $this->is_wpmailsmtp_pro_plugin( $slug ) ) {
			return;
		}

		$translations = $this->get_translations( $slug );

		if ( empty( $translations['translations'] ) ) {
			return;
		}

		$this->download_plugin_translations( $slug, $translations['translations'] );
	}
}
