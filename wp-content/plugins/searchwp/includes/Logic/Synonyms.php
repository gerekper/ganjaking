<?php
/**
 * SearchWP Synonyms.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Logic;

use SearchWP\Query;
use SearchWP\Settings;
use SearchWP\Utils;
use SearchWP\Support\Str;

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
	 *
	 * @var string
	 */
	private $language_code;

	/**
	 * A flag to track the detection of the synonyms in a search string.
	 *
	 * @since 4.2.3
	 *
	 * @var bool
	 */
	private $found_synonym = false;

	/**
	 * Original search string before applying synonyms modifications.
	 *
	 * @since 4.2.3
	 *
	 * @var string
	 */
	private $original_search_string = '';

	/**
	 * Synonyms.
	 *
	 * @since 4.0
	 *
	 * @var array
	 */
	private $synonyms = [];

	/**
	 * Grouped synonyms.
	 *
	 * @since 4.2.3
	 *
	 * @var array
	 */
	public static $synonym_groups = [];

	/**
	 * Synonyms constructor.
	 *
	 * @since 4.0
	 */
	public function __construct() {

		// TODO: Build in support for multilanguage setups (WPML, Polylang, soon to be core).
		$this->language_code = strtolower( substr( get_locale(), 0, 2 ) );

		// TODO: Running the hooks will be moved out of the constructor as a part of the plugin bootstrap rewiring.
		$this->hooks();
	}

	/**
	 * Class hooks.
	 *
	 * @since 4.2.3
	 *
	 * @return void
	 */
	private function hooks() {

		// Apply synonyms to query search string.
		add_filter( 'searchwp\query\search_string', [ $this, 'apply' ], 5, 2 );
	}

	/**
	 * Applies synonyms to tokens.
	 *
	 * @since 4.0
	 *
	 * @param string $search_string Original search string.
	 * @param Query  $query         Query object.
	 *
	 * @return string Synonyms applied.
	 */
	public function apply( string $search_string, Query $query ): string {

		$search_string = Str::lower( $search_string );

		$query->set_debug_data( 'string.synonyms.before', $search_string );

		$this->set_original_search_string( $search_string );
		$this->set_initial_synonym_groups( $search_string );

		$this->set_synonyms( $search_string, $query );
		$this->set_partial_matches( $search_string, $query );

		$search_string = $this->apply_synonyms( $search_string, $query );
		$search_string = $this->remove_duplicate_words_from_string( $search_string );

		$this->remove_duplicate_tokens_from_token_groups();

		$query->set_debug_data( 'string.synonyms.after', $search_string );

		return $search_string;
	}

	/**
	 * Getter for saved Synonyms.
	 *
	 * @since 4.0
	 *
	 * @return array
	 */
	public function get(): array {

		$synonyms = Settings::get( 'synonyms' );

		return $synonyms === false ? [] : $this->normalize( $synonyms );
	}

	/**
	 * Normalizes synonym model.
	 *
	 * @since 4.0
	 *
	 * @param array $synonyms Incoming synonyms.
	 *
	 * @return array Normalized synonyms.
	 */
	public function normalize( array $synonyms = [] ): array {

		return array_values( array_filter( array_map( function( $synonym ) {
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
		}, $synonyms ) ) );
	}

	/**
	 * Updates saved synonyms.
	 *
	 * @since 4.0
	 *
	 * @param array $synonyms Synonyms to save.
	 *
	 * @return array Saved Synonyms.
	 */
	public function save( array $synonyms = [] ): array {

		$synonyms = $this->normalize( $synonyms );

		Settings::update( 'synonyms', $synonyms );

		return $synonyms;
	}

	/**
	 * Save the original search string for future use.
	 *
	 * @since 4.2.3
	 *
	 * @param string $search_string Search string.
	 *
	 * @return void
	 */
	private function set_original_search_string( string $search_string ) {

		$this->original_search_string = $search_string;
	}

	/**
	 * Set initial empty synonym groups to fill them with synonyms later.
	 *
	 * @since 4.2.3
	 *
	 * @param string $search_string Search string.
	 *
	 * @return void
	 */
	private function set_initial_synonym_groups( string $search_string ) {

		// Remove any quote if present.
		$search_string = Str::remove_quotes( $search_string );

		self::$synonym_groups = array_fill_keys( explode( ' ', $search_string ), [] );
	}

	/**
	 * Sets the synonyms for this application.
	 *
	 * @since 4.0
	 * @since 4.2.3 Changed public set() to private set_synonyms()
	 *
	 * @param string $search_string Query search string.
	 * @param Query  $query         Query object.
	 *
	 * @return void Synonyms.
	 */
	private function set_synonyms( string $search_string, Query $query ) {

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
	}

	/**
	 * Applies partial matches to synonyms.
	 *
	 * @since 4.1
	 * @since 4.2.3 Changed to private method
	 *
	 * @param string $search_string Query search string.
	 * @param Query  $query         Query object.
	 *
	 * @return void
	 */
	private function set_partial_matches( string $search_string, Query $query ) {

		if ( empty( $this->synonyms ) ) {
			return;
		}

		$data = [
			'search' => $search_string,
			'query'  => $query,
		];

		// Apply our (wildcard-based) partial matching by default.
		if ( ! apply_filters( 'searchwp\synonyms\partial_matches', true, $data ) ) {
			return;
		}

		$synonyms = $this->synonyms;

		// Pad the search string to prevent overrun.
		$_search_string = ' ' . $this->original_search_string . ' ';

		foreach ( $synonyms as $index => $synonym ) {
			$sources = array_map( 'trim', explode( ',', $synonym['sources'] ) );

			foreach ( $sources as $source ) {
				// In order for partial matching to apply, a wildcard character (*) is used
				// because there are too many common cases where more generalized partial
				// matching has too many overruns and it triggers unwanted synonym hits.
				if ( ! Str::contains( $source, '*' ) ) {
					continue;
				}

				// Convert the wildcard into something that won't be double encoded.
				$placeholder     = Utils::get_placeholder( false );
				$source          = Str::remove_quotes( $source );
				$original_source = $source;
				$source          = str_replace( '*', $placeholder, $source );

				$needle = $original_source[0] !== '*' ?
					str_replace( $placeholder, '\S*\s', preg_quote( $source, '/' ) ) :
					str_replace( $placeholder, '\s\S*', preg_quote( $source, '/' ) );

				$pattern = '/' . $needle . '/ius';
				$term    = ' ' . Str::remove_quotes( $_search_string ) . ' ';

				if ( 1 === preg_match( $pattern, $term, $matches ) ) {
					$new_sources = implode( ',', array_map( 'trim', $matches ) );
					$this->synonyms[ $index ]['sources'] = str_replace( $original_source, $new_sources, $this->synonyms[ $index ]['sources'] );
				}
			}
		}
	}

	/**
	 * Apply synonyms to a search string.
	 *
	 * @since 4.2.3
	 *
	 * @param string $search_string Search string.
	 * @param Query  $query         Query object.
	 *
	 * @return string
	 */
	private function apply_synonyms( string $search_string, Query $query ): string {

		$synonyms = $this->synonyms;

		if ( empty( $synonyms ) ) {
			self::$synonym_groups = [];
			return $search_string;
		}

		foreach ( $synonyms as $synonym ) {
			// Multiple sources can be set using comma separation.
			$sources = array_filter( array_map( 'trim', explode( ',', $synonym['sources'] ) ) );

			if ( empty( $sources ) ) {
				continue;
			}

			// Reset the found synonyms flag before using it.
			$this->found_synonym = false;

			$search_string = $this->process_synonym_sources( $search_string, $sources, $synonym );

			// Assume that one synonym replacement is enough and in doing so prevent
			// redundant synonym application, but also base that on a hook to allow
			// developers to 'stack' synonyms when that's what they want to do.
			if ( apply_filters( 'searchwp\synonyms\aggressive', true ) && $this->found_synonym ) {
				break;
			}
		}

		// If no synonyms match was found empty the synonym groups
		if ( empty( array_values( self::$synonym_groups ) ) ) {
			self::$synonym_groups = [];
		}

		return $search_string;
	}

	/**
	 * Process single synonym sources.
	 *
	 * @since 4.2.3
	 *
	 * @param string $search_string Search string.
	 * @param array  $sources       Single synonym sources.
	 * @param array  $synonym       Single synonym data.
	 *
	 * @return string
	 */
	private function process_synonym_sources( string $search_string, array $sources, array $synonym ): string {

		// Iterate over the sources to see if there's a match.
		foreach ( $sources as $source ) {

			// If source contains a wildcard we can skip it as it was already processed for partial matches.
			if ( Str::contains( $source, '*' ) ) {
				continue;
			}

			$source = Str::lower( $source );

			// Strip quotes from the source and check if the source is present in the string.
			// If there is no match we can skip it.
			if ( ! preg_match( '/\s' . preg_quote(	Str::remove_quotes( $source ), '/' ) . '\s/iu',
				' ' . Str::remove_quotes( $search_string ) . ' '
			) ) {
				continue;
			}

			// If there's a space in the search string and the synonym source opt to replace only the whole source.
			if ( Str::contains( $search_string, ' ' ) && Str::contains( $source, ' ' ) ) {
				$search_string = $this->process_compound_source( $search_string, $source, $synonym );
			} else {
				$search_string = $this->process_regular_source( $search_string, $source, $synonym );
			}
		}

		return $search_string;
	}

	/**
	 * Process single compound (consisting of several words) source.
	 *
	 * @since 4.2.3
	 *
	 * @param string $search_string Search string.
	 * @param string $source        Single source.
	 * @param array  $synonym       Single synonym data.
	 *
	 * @return string
	 */
	private function process_compound_source( string $search_string, string $source, array $synonym ): string {

		$search_string_before = $search_string;

		if ( $this->is_not_strict_synonym_match( $search_string, $source ) ) {
			return $search_string;
		}

		if ( $synonym['replace'] ) {
			$search_string = $this->process_compound_source_replace( $search_string, $source, $synonym );
		} else {
			$search_string = $this->process_compound_source_no_replace( $search_string, $synonym );
		}

		// If the synonyms are present in the group as a source they should be removed.
		foreach ( explode( ' ', $source ) as $source_tokens ) {
			$source_tokens = Str::remove_quotes( $source_tokens );
			if ( isset( self::$synonym_groups[ $source_tokens ] ) ) {
				unset( self::$synonym_groups[ $source_tokens ] );
			}
		}

		// We can now add the synonyms tokens as new sources.
		$this->add_synonym_tokens_to_synonym_groups( $synonym );

		if ( $search_string_before !== $search_string ) {
			$this->found_synonym = true;
		}

		return $search_string;

		// TODO: use searchwp\query\tokens\limit hook to adjust limit based on count( $synonym['synonyms'] ) if necessary?
	}

	/**
	 * Process compound (consisting of several words) source replacing it with a synonym.
	 *
	 * @since 4.2.4
	 *
	 * @param string $search_string Search string.
	 * @param string $source        Single source.
	 * @param array  $synonym       Single synonym data.
	 *
	 * @return string
	 */
	private function process_compound_source_replace( string $search_string, string $source, array $synonym ): string {

		// Non strict quotes on synonyms sources.
		if ( ! apply_filters( 'searchwp\synonyms\strict', false ) ) {

			// Match quotes between search string and source.
			$source = Str::remove_quotes( $source );
			$source = Str::contains( $search_string, '"' ) ? '"' . $source . '"' : $source;
		}

		return $this->replace_source_with_synonyms_in_string( $search_string, $source, $synonym );
	}

	/**
	 * Process compound (consisting of several words) source without replacing it with a synonym.
	 *
	 * @since 4.2.4
	 *
	 * @param string $search_string Search string.
	 * @param array  $synonym       Single synonym data.
	 *
	 * @return string
	 */
	private function process_compound_source_no_replace( string $search_string, array $synonym ): string {

		return $search_string . ' ' . $synonym['synonyms'];
	}

	/**
	 * Process regular non-compound (one word) source.
	 *
	 * @since 4.2.3
	 *
	 * @param string $search_string Search string.
	 * @param string $source        Single source.
	 * @param array  $synonym       Single synonym data.
	 *
	 * @return string
	 */
	private function process_regular_source( string $search_string, string $source, array $synonym ): string {

		$search_string_before = $search_string;

		if ( $synonym['replace'] ) {
			$search_string = $this->process_regular_source_replace( $search_string, $source, $synonym );
		} else {
			$search_string = $this->process_regular_source_no_replace( $search_string, $source, $synonym );
		}

		if ( $search_string_before !== $search_string ) {
			$this->found_synonym = true;
		}

		return $search_string;

		// TODO: use searchwp\query\tokens\limit hook to adjust limit based on count( $synonym['synonyms'] ) if necessary?
	}

	/**
	 * Process regular non-compound (one word) source replacing it with a synonym.
	 *
	 * @since 4.2.3
	 *
	 * @param string $search_string Search string.
	 * @param string $source        Single source.
	 * @param array  $synonym       Single synonym data.
	 *
	 * @return string
	 */
	private function process_regular_source_replace( string $search_string, string $source, array $synonym ): string {

		if ( $this->is_not_strict_synonym_match( $search_string, $source ) ) {
			return $search_string;
		}

		$search_string = $this->replace_source_with_synonyms_in_string( $search_string, $source, $synonym );

		if ( isset( self::$synonym_groups[ $source ] ) ) {
			unset( self::$synonym_groups[ $source ] );
		}

		$this->add_synonym_tokens_to_synonym_groups( $synonym );

		return $search_string;
	}

	/**
	 * Process regular non-compound (one word) source without replacing it with a synonym.
	 *
	 * @since 4.2.3
	 *
	 * @param string $search_string Search string.
	 * @param string $source        Single source.
	 * @param array  $synonym       Single synonym data.
	 *
	 * @return string
	 */
	private function process_regular_source_no_replace( string $search_string, string $source, array $synonym ): string {

		$search_string .= ' ' . $synonym['synonyms'];

		if ( in_array( $source, array_keys( self::$synonym_groups ), true ) ) {
			self::$synonym_groups[ $source ] = array_merge( self::$synonym_groups[ $source ], array_map( 'trim', explode( ',', $synonym['synonyms'] ) ) );
		} else {
			self::$synonym_groups[ $source ] = array_map( 'trim', explode( ',', $synonym['synonyms'] ) );
		}

		return $search_string;
	}

	/**
	 * Replaces the synonym source with the synonyms in the string.
	 *
	 * @since 4.2.4
	 *
	 * @param string $search_string Search string.
	 * @param string $source        Single source.
	 * @param array  $synonym       Single synonym data.
	 *
	 * @return string
	 */
	private function replace_source_with_synonyms_in_string( string $search_string, string $source, array $synonym ): string {

		return trim(
			preg_replace(
				'/\s' . preg_quote( $source, '/' ) . '\s/iu',
				' ' . $synonym['synonyms'] . ' ',
				' ' . $search_string . ' '
			)
		);
	}

	/**
	 * Add tokens from a specific synonym to the synonym groups.
	 *
	 * @since 4.2.4
	 *
	 * @param array $synonym Single synonym data.
	 */
	private function add_synonym_tokens_to_synonym_groups( array $synonym ) {

		$synonyms = Str::remove_quotes( $synonym['synonyms'] );

		$replace_synonyms = explode( ' ', preg_replace( '/[,\s]+/', ' ', $synonyms ) );
		$new_tokens       = array_fill_keys( $replace_synonyms, [] );

		self::$synonym_groups = array_merge( self::$synonym_groups, $new_tokens );
	}

	/**
	 * Remove duplicate token between sources and synonyms from token groups.
	 *
	 * @since 4.2.3
	 *
	 * @return void
	 */
	private function remove_duplicate_tokens_from_token_groups(): void {

		$synonym_groups_keys = array_keys( self::$synonym_groups );

		foreach ( self::$synonym_groups as $tokens ) {
			foreach ( $tokens as $token ) {
				if ( in_array( $token, $synonym_groups_keys, true ) && empty( self::$synonym_groups[ $token ] ) ) {
					unset( self::$synonym_groups[ $token ] );
				}
			}
		}
	}

	/**
	 * Returns true if source and search string both have or both don't have quotes and the synonyms are strict.
	 *
	 * @since 4.2.4
	 *
	 * @param string $search_string Search string.
	 * @param string $source        Single source.
	 *
	 * @return bool
	 */
	private function is_not_strict_synonym_match( $search_string, $source ): bool {

		return apply_filters( 'searchwp\synonyms\strict', false )
			&& Str::contains( $search_string, '"' ) !== Str::contains( $source, '"'	);
	}

	/**
	 * Remove duplicate words from a string preserving quoted phrases.
	 *
	 * @since 4.2.4
	 *
	 * @param string $string Input string.
	 *
	 * @return string
	 */
	private function remove_duplicate_words_from_string( string $string ): string {

		// Extract any single keyword and preserve quoted phrases as a single entry.
		// "[^"]+" matches any quoted substring.
		// [^\s,]+ matches any single keyword separated by commas or spaces.
		preg_match_all( '/"[^"]+"|[^\s,]+/', $string, $matches );

		// Remove duplicates and return the final string.
		return implode( ' ', array_unique( $matches[0] ) );
	}
}
