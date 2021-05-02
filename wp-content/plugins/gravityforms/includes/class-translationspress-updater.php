<?php

namespace Gravity_Forms\Gravity_Forms;

/**
 * Allows to download translations from TranslationsPress
 * This is a modified version of the library available at https://github.com/WP-Translations/t15s-registry
 * This version aims to be compatible with PHP 5.2, and supports only plugins.
 *
 * @since 2.5
 */
class TranslationsPress_Updater {

	const T15S_TRANSIENT_KEY = 't15s-registry-gforms';
	const T15S_API_URL       = 'https://packages.translationspress.com/rocketgenius/packages.json';

	private $type     = 'plugin';
	private $slug     = '';
	private $language = '';


	/* Adds a new project to load translations for.
	*
	* @since 2.5
	*
	* @param string $slug    Project directory slug.
	* @param string $language The new language to install, if user is switching languages.
	* @param string $api_url Full GlotPress API URL for the project.
	*/
	public function __construct( $slug, $language = '' ) {
		$this->slug     = $slug;
		$this->language = $language;

		add_action( 'init', array( $this, 'register_clean_translations_cache' ), 9999 );
		add_filter( 'translations_api', array( $this, 'translations_api' ), 10, 3 );
		add_filter( 'site_transient_update_' . $this->type . 's', array( $this, 'site_transient_update_plugins' ) );
	}

	/**
	 * Short-circuits translations API requests for private projects.
	 *
	 * @since 2.5
	 *
	 * @param bool|array $result         The result object. Default false.
	 * @param string     $requested_type The type of translations being requested.
	 * @param object     $args           Translation API arguments.
	 * @return bool|array
	 */
	public function translations_api( $result, $requested_type, $args ) {
		if ( $this->type . 's' === $requested_type && $this->slug === $args['slug'] ) {
			return self::get_translations( self::T15S_API_URL );
		}

		return $result;
	}

	/**
	 * Filters the translations transients to include the private plugin or theme.
	 *
	 * @see wp_get_translation_updates()
	 *
	 * @since 2.5
	 *
	 * @param bool|array $value The transient value.
	 */
	public function site_transient_update_plugins( $value ) {
		if ( ! $value ) {
			$value = new \stdClass();
		}

		if ( ! isset( $value->translations ) ) {
			$value->translations = array();
		}

		$translations = (array) self::get_translations( self::T15S_API_URL );

		if ( ! isset( $translations['projects'][ $this->slug ]['translations'] ) ) {
			return $value;
		}

		$installed_translations = wp_get_installed_translations( $this->type . 's' );

		foreach ( $translations['projects'][ $this->slug ]['translations'] as $translation ) {

			if ( in_array( $translation['language'], get_available_languages() ) ) {
				if ( isset( $installed_translations[ $this->slug ][ $translation['language'] ] ) && $translation['updated'] ) {
					$local  = new \DateTime( $installed_translations[ $this->slug ][ $translation['language'] ]['PO-Revision-Date'] );
					$remote = new \DateTime( $translation['updated'] );

					if ( $local >= $remote ) {
						continue;
					}
				}

				$translation['type'] = $this->type;
				$translation['slug'] = $this->slug;

				$value->translations[] = $translation;
			}
		}

		return $value;
	}

	/**
	 * Registers actions for clearing translation caches.
	 *
	 * @since 2.5
	 */
	public function register_clean_translations_cache() {
		add_action( 'set_site_transient_update_plugins', array( $this, 'clean_translations_cache' ) );
		add_action( 'delete_site_transient_update_plugins', array( $this, 'clean_translations_cache' ) );
	}

	/**
	 * Clears existing translation cache.
	 *
	 * @since 2.5
	 */
	public function clean_translations_cache() {
		$translations = get_site_transient( self::T15S_TRANSIENT_KEY );

		if ( ! is_object( $translations ) ) {
			return;
		}

		/*
		 * Don't delete the cache if the transient gets changed multiple times
		 * during a single request. Set cache lifetime to maximum 15 seconds.
		 */
		$cache_lifespan   = DAY_IN_SECONDS;
		$time_not_changed = isset( $translations->_last_checked ) && ( time() - $translations->_last_checked ) > $cache_lifespan;

		if ( ! $time_not_changed ) {
			return;
		}

		delete_site_transient( self::T15S_TRANSIENT_KEY );
	}

	/**
	 * Gets the translations for a given project.
	 *
	 * @since 2.5
	 *
	 * @param string $type Project type. Either plugin or theme.
	 * @param string $slug Project directory slug.
	 * @param string $url  Full GlotPress API URL for the project.
	 * @return array Translation data.
	 */
	public static function get_translations( $url ) {
		$translations = get_site_transient( self::T15S_TRANSIENT_KEY );

		if ( false !== $translations ) {
			return $translations;
		}

		if ( ! is_object( $translations ) ) {
			$translations = new \stdClass();
		}

		$result = json_decode( wp_remote_retrieve_body( wp_remote_get( $url, array( 'timeout' => 3 ) ) ), true );

		// Nothing found.
		if ( ! is_array( $result ) ) {
			$result = array();
		}

		$translations->projects      = $result;
		$translations->_last_checked = time();

		set_site_transient( self::T15S_TRANSIENT_KEY, $translations );
		return $result;
	}

	public static function download_package( $slug, $language = '' ) {

		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			require_once ABSPATH . '/wp-admin/includes/admin.php';

			if ( ! \WP_Filesystem() ) {
				return false;
			}
		}

		$locale = '' == $language ? $locale = get_user_locale() : $language;

		$translations = (array) self::get_translations( self::T15S_API_URL );

		if ( isset( $translations['projects'][ $slug ] ) ) {

			foreach ( $translations['projects'][ $slug ]['translations'] as $translation ) {

				if ( $locale == $translation['language'] ) {

					$url = $translation['package'];
					$lang_dir = WP_LANG_DIR . '/plugins/';
					if ( ! $wp_filesystem->is_dir( $lang_dir ) ) {
						$wp_filesystem->mkdir( $lang_dir, FS_CHMOD_DIR );
					}
					$zipPath = $lang_dir . $slug . '-' . $locale . '.zip';
					$zipContent = download_url( $url, $timeout  = 300 );
					$wp_filesystem->copy( $zipContent, $zipPath, true, FS_CHMOD_FILE );
					$translations_files = unzip_file( $zipPath, WP_LANG_DIR . '/plugins/' );
					unlink( $zipPath );

				}

			}

		}

	}

}
