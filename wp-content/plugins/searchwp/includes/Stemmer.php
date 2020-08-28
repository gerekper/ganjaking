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
	 * Supported language codes and their correlating class name.
	 *
	 * @since 4.0
	 * @package SearchWP
	 * @var array
	 */
	private $supported_language_codes = array(
		'en' => 'English',
		'da' => 'Danish',
		'nl' => 'Dutch',
		'fr' => 'French',
		'de' => 'German',
		'it' => 'Italian',
		'nb' => 'Norwegian',
		'nn' => 'Norwegian',
		'pt' => 'Portuguese',
		'ro' => 'Romanian',
		'ru' => 'Russian',
		'es' => 'Spanish',
		'sv' => 'Swedish',
	);

	/**
	 * The stemmer for this language code.
	 *
	 * @since 4.0
	 * @package SearchWP
	 * @var bool|object
	 */
	private $localized_stemmer = false;

	/**
	 * Constructor.
	 *
	 * @since 4.0
	 */
	public function __construct() {
		// Determine which locale we're using.
		$site_locale   = apply_filters( 'searchwp\stemmer\locale', get_locale() );
		$language_code = strtolower( substr( $site_locale, 0, 2 ) );

		if ( array_key_exists( $language_code, $this->supported_language_codes ) ) {
			$stemmer = '\SearchWP\Dependencies\Wamania\Snowball\\' . $this->supported_language_codes[ $language_code ];
			if ( class_exists( $stemmer ) ) {
				$this->localized_stemmer = new $stemmer();
			}
		}
	}

	/**
	 * Generates a stem for the submitted word.
	 *
	 * @since 4.0
	 * @param string $word The word to stem.
	 * @return string The stemmed word.
	 */
	public function stem( string $word ) {
		$custom_stem = apply_filters( 'searchwp\stemmer\custom', false, $word );
		if ( $custom_stem ) {
			return $custom_stem;
		}

		if ( false === $this->localized_stemmer ) {
			do_action( 'searchwp\debug\log', 'No stemmer found', 'stemmer' );

			return $word;
		}

		return $this->localized_stemmer->stem( $word );
	}

	/**
	 * Getter for supported language codes.
	 *
	 * @since 4.0
	 * @return array The language codes.
	 */
	function get_supported_language_codes() {
		return array_keys( $this->supported_language_codes );
	}
}
