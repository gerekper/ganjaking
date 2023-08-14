<?php
/**
 * SearchWP Stemmer.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

/**
 * Class Stemmer generates keyword stems.
 *
 * @since 4.0
 */
final class Stemmer {

	/**
	 * The stemmer for this language code.
	 *
	 * @since 4.0
	 *
	 * @var bool|object
	 */
	private $localized_stemmer = false;

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 */
	public function __construct() {

		if ( ! class_exists( '\SearchWP\Dependencies\Wamania\Snowball\StemmerFactory' ) ) {
			return;
		}

		// Determine which locale we're using.
		$site_locale   = apply_filters( 'searchwp\stemmer\locale', get_locale() );
		$language_code = strtolower( substr( $site_locale, 0, 2 ) );

		foreach ( \SearchWP\Dependencies\Wamania\Snowball\StemmerFactory::LANGS as $class_name => $iso_codes ) { // phpcs:ignore WPForms.PHP.BackSlash.UseShortSyntax
			if ( class_exists( $class_name ) && in_array( $language_code, $iso_codes, true ) ) {
				$this->set_stemmer( $class_name );

				return;
			}
		}
	}

	/**
	 * Sets a stemmer class.
	 *
	 * @since 4.2.9
	 *
	 * @param string $class_name Class name of the stemmer.
	 */
	private function set_stemmer( string $class_name ) {

		// This file is needed for PHP Stemmer library to work properly.
		$utf8_lib_bootstrap_file = SEARCHWP_PLUGIN_DIR . '/lib/vendor/voku/portable-utf8/bootstrap.php';

		if ( ! file_exists( $utf8_lib_bootstrap_file ) ) {
			return;
		}

		require $utf8_lib_bootstrap_file;

		$this->localized_stemmer = new $class_name();
	}

	/**
	 * Generates a stem for the submitted word.
	 *
	 * @since 4.0
	 *
	 * @param string $word The word to stem.
	 *
	 * @return string The stemmed word.
	 */
	public function stem( string $word ) {

		$custom_stem = apply_filters( 'searchwp\stemmer\custom', false, $word );
		if ( $custom_stem ) {
			return $custom_stem;
		}

		if ( $this->localized_stemmer === false ) {
			do_action( 'searchwp\debug\log', 'No stemmer found', 'stemmer' );

			return $word;
		}

		try {
			$stem = $this->localized_stemmer->stem( $word );
		} catch ( \Exception $e ) {
			return $word;
		}

		return $stem;
	}

	/**
	 * Getter for supported language codes.
	 *
	 * @since 4.0
	 *
	 * @return array The language codes.
	 */
	function get_supported_language_codes() {

		if ( ! class_exists( '\SearchWP\Dependencies\Wamania\Snowball\StemmerFactory' ) ) {
			return [];
		}

		return array_column( \SearchWP\Dependencies\Wamania\Snowball\StemmerFactory::LANGS, 0 ); // phpcs:ignore WPForms.PHP.BackSlash.UseShortSyntax
	}
}
