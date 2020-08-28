<?php

/**
 * SearchWP Synonyms.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Logic;

use SearchWP\Query;
use SearchWP\Utils;

/**
 * Class Synonyms is responsible for applying synonyms to Tokens.
 *
 * @since 4.0
 */
class Synonyms {

	/**
	 * The language code.
	 *
	 * @since 4.0
	 * @var string
	 */
	private $language_code;

	/**
	 * Synonyms.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $synonyms = [];

	/**
	 * Synonyms constructor.
	 *
	 * @since 4.0
	 */
	function __construct() {
		$this->language_code = strtolower( substr( get_locale(), 0, 2 ) );

		// TODO: Build in support for multilanguage setups (WPML, Polylang, soon to be core).

		// Apply synonyms to query search string.
		add_filter( 'searchwp\query\search_string', [ $this, 'apply' ], 5, 2 );
	}

	/**
	 * Applies synonyms to tokens.
	 *
	 * @since 4.0
	 * @param string $search_string Original search string.
	 * @return string Synonyms applied.
	 */
	public function apply( string $search_string, Query $query ) {
		$synonyms = $this->set( $search_string, $query );

		// If there are quoted phrases, limit applicable synonyms.
		if ( $phrases = Utils::search_string_has_phrases( $search_string, $query ) ) {
			$synonyms_filtered = array_values( array_filter( $synonyms, function( $synonym ) use ( $phrases ) {
				return ! empty( array_filter( $phrases, function( $phrase ) use ( $synonym ) {
					return false !== strpos( $synonym['sources'], $phrase );
				} ) );
			} ) );

			if ( ! empty( $synonyms_filtered ) || apply_filters( 'searchwp\synonyms\strict', false ) ) {
				$synonyms = $synonyms_filtered;
			}
		}

		if ( ! is_array( $synonyms ) || empty( $synonyms ) ) {
			return $search_string;
		}

		foreach ( $synonyms as $synonym ) {
			// Multiple sources can be set using comma separation.
			$sources = array_filter( array_map( 'trim', explode( ',', $synonym['sources'] ) ) );

			if ( empty( $sources ) ) {
				continue;
			}

			// Iterate over the sources to see if there's a match.
			foreach ( $sources as $source ) {
				// If we're not replacing, prepend this source to the synonyms.
				if ( ! $synonym['replace'] ) {
					// If this source is a quoted phrase, remove the quotes first.
					$synonym['synonyms'] = str_replace( '"', '', $source ) . ' ' . $synonym['synonyms'];
				}

				// Process phrases if applicable.
				if ( $synonym_source_phrases = Utils::get_phrases_from_string( $source ) ) {
					foreach( $synonym_source_phrases as $synonym_source_phrase ) {
						$search_string = preg_replace( '/\b' . preg_quote( $synonym_source_phrase, '/' ) . '\b/i', $synonym['synonyms'], $search_string );

						// Remove this phrase from the source for subsequent processing.
						$source = preg_replace( '/\b' . preg_quote( $synonym_source_phrase, '/' ) . '\b/i', '', $source );
					}
				}

				// If there's a space in the search string and the synonym source opt to replace only the whole source.
				$compound_source = false;
				if ( false !== strpos( $search_string, ' ' ) && false !== strpos( $source, ' ' ) ) {
					$compound_source = true;
					$search_string   = preg_replace( '/\b' . preg_quote( $source, '/' ) . '\b/i', $synonym['synonyms'], $search_string );
				}

				if ( ! $compound_source ) {
					// Handle synonym replacement where applicable.
					$search_string = implode( ' ', array_map( function( $token ) use ( $synonym, $source ) {
						return preg_replace( '/\b' . preg_quote( $source, '/' ) . '\b/i', $synonym['synonyms'], $token );
					}, explode( ' ', $search_string ) ) );
				}
			}
		}

		return $search_string;
	}

	/**
	 * Sets the synonyms for this application.
	 *
	 * @since 4.0
	 * @param string $search_string Query search string.
	 * @param Query $query Query
	 * @return array Synonyms.
	 */
	private function set( string $search_string, Query $query ) {
		$synonyms = $this->get();

		// Allow developers to customize.
		$synonyms = (array) apply_filters( 'searchwp\synonyms', $synonyms, [
			'search_string' => $search_string,
			'query'         => $query,
		] );

		// Ensure valid format.
		$this->synonyms = array_filter( $synonyms, function( $synonym ) {
			return isset( $synonym['sources'] )
				&& isset( $synonym['synonyms'] )
				&& isset( $synonym['replace'] );
		} );

		return $this->synonyms;
	}

	/**
	 * Updates saved synonyms.
	 *
	 * @since 4.0
	 * @param array $synonyms Synonyms to save.
	 * @return array Saved Synonyms.
	 */
	public function save( array $synonyms = [] ) {
		$synonyms = $this->normalize( $synonyms );

		\SearchWP\Settings::update( 'synonyms', $synonyms );

		return $synonyms;
	}

	/**
	 * Normalizes synonym model.
	 *
	 * @since 4.0
	 * @param array $synonyms Incoming synonyms.
	 * @return array Normalized synonyms.
	 */
	public function normalize( array $synonyms = [] ) {
		return array_filter( array_map( function( $synonym ) {
			if ( empty( $synonym['sources'] ) || empty( $synonym['synonyms'] ) ) {
				return false;
			}

			return [
				'sources' => implode( ', ', array_map( function( $source ) {
					return trim( sanitize_text_field( $source ) );
				}, explode( ',', $synonym['sources'] ) ) ),
				'synonyms' => implode( ', ', array_map( function( $source ) {
					return trim( sanitize_text_field( $source ) );
				}, explode( ',', $synonym['synonyms'] ) ) ),
				'replace' => ! empty( $synonym['replace'] )
			];
		}, $synonyms ) );
	}

	/**
	 * Getter for saved Synonyms.
	 *
	 * @since 4.0
	 * @return (string|false)[][]
	 */
	public function get() {
		$synonyms = \SearchWP\Settings::get( 'synonyms' );

		return false === $synonyms ? [] : $this->normalize( $synonyms );
	}
}
