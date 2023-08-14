<?php

/**
 * SearchWP Entry.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

use SearchWP\Source;

/**
 * Class Entry is an instance of a Source. It contains a model of the Source but also
 * all of the applicable data for this Entry for the Source.
 *
 * @since 4.0
 */
class Entry {

	/**
	 * The Source model for this Entry.
	 *
	 * @since 4.0
	 * @var   Source
	 */
	private $source;

	/**
	 * The ID of this Entry.
	 *
	 * @since 4.0
	 * @var   int|string
	 */
	private $id;

	/**
	 * The data for this Entry as defined by its Attributes.
	 *
	 * @since 4.0
	 * @var   array
	 */
	private $data;

	/**
	 * Entry constructor.
	 *
	 * @since 4.0
	 * @param string|Source $source         The Source for this Entry.
	 * @param string|int    $id             The ID for this Entry.
	 * @param boolean       $get_data       Whether to retrieve Entry data on instantiation.
	 * @param boolean       $all_attributes Whether to retrieve all registered Attribute
	 *                                          data (true) or only Attributes used in
	 *                                          an Engine (false).
	 */
	function __construct( $source, $id, $get_data = true, $all_attributes = false ) {
		if ( ! $source instanceof Source && is_string( $source ) ) {
			$index  = \SearchWP::$index;
			$source = $index->get_source_by_name( $source );
		}

		$this->id     = $id;
		$this->source = $source;

		if ( $get_data ) {
			$this->update_data( $all_attributes );
		}
	}

	/**
	 * Getter for Entry ID.
	 *
	 * @since 4.0
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Getter for Entry Source.
	 *
	 * @since 4.0
	 * @return Source
	 */
	public function get_source() {
		return $this->source;
	}

	/**
	 * Iterates over the Attributes for this Entry Source and retrieves the data for each.
	 *
	 * @since 4.0
	 * @param boolean $all_attributes Whether to retrieve all registered Attribute data (true)
	 *                                or only Attributes used in an Engine (false).
	 * @return void
	 */
	public function update_data( $all_attributes = false, $skip_tokenizing = false ) {
		$this->data = [];

		do_action( 'searchwp\entry\update_data\before', $this );

		try {
			foreach ( $this->source->get_attributes() as $attribute ) {
				// If this Attribute isn't used anywhere; it's overhead.
				if ( ! $all_attributes && ! Utils::any_engine_has_source_attribute( $attribute, $this->source ) ) {
					continue;
				}

				// If there aren't any options, we can just retrieve the data and continue.
				if ( false === $attribute->get_options() ) {
					$this->data[ $attribute->get_name() ] = $attribute->get_data( $this->id, false, $skip_tokenizing );
					continue;
				}

				// If there are options, we need to iterate over the chosen options.
				$attribute_data = [];
				$chosen_options = Utils::get_global_attribute_options_settings( $attribute, $this->source );

				if ( ! empty( $chosen_options ) ) {

					// We need to process partial matches here so as to facilitate keeping the individual option data separate.
					$partial_match_chosen_options = array_filter( $chosen_options, function( $chosen_option ) {
						return false !== strpos( $chosen_option, '*' );
					} );

					if ( ! empty( $partial_match_chosen_options ) ) {
						// We need to remove the partial match options and replace with applied partial matches.
						$chosen_options = array_diff( $chosen_options, $partial_match_chosen_options );

						// Append the actual options for the partial matches.
						$actual_chosen_options = array_unique(
							call_user_func_array(
								'array_merge',
								array_map( function( $partial_chosen_option ) use ( $attribute ) {
									return array_map( function( $option ) {
										return $option->get_value();
									}, $attribute->get_options( $partial_chosen_option ) );
								}, $partial_match_chosen_options )
							)
						);

						$chosen_options = array_merge( $chosen_options, $actual_chosen_options );
					}

					foreach ( $chosen_options as $chosen_option ) {
						$attribute_data[ $chosen_option ] = $attribute->get_data( $this->id, $chosen_option, $skip_tokenizing );
					}
				}

				$this->data[ $attribute->get_name() ] = $attribute_data;

				unset( $attribute_data );
			}
		} catch ( \Exception $e ) {
			do_action( 'searchwp\debug\log', 'Error retrieving Entry data:', 'entry' );
			do_action( 'searchwp\debug\log', $e->getMessage(), 'entry' );
		}

		// Ensure that all Entry data is tokenized.
		$this->data = array_map( function( $attribute_data ) use ( $skip_tokenizing ) {
			if ( $attribute_data instanceof Tokens || $skip_tokenizing ) {
				return $attribute_data;
			}

			// Support Attributes with Options.
			if ( is_array( $attribute_data ) ) {
				return array_map( function( $attribute_option_data ) {
					if ( $attribute_option_data instanceof Tokens ) {
						return $attribute_option_data;
					}

					return Utils::tokenize( $attribute_option_data );
				}, $attribute_data );
			}

			// This was just a variable; tokenize it.
			return Utils::tokenize( $attribute_data );
		}, (array) apply_filters( 'searchwp\entry\data', $this->data, $this ) );
	}

	/**
	 * Getter for Entry data.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function get_data( $skip_tokenizing = false, $skip_cache = false ) {
		// Utilize the cache if applicable.
		$cache_key = 'searchwp_entry_' . $this->source->get_name() . '_' . $this->id;

		if ( $skip_tokenizing ) {
			$cache_key .= '_notokens';
		}

		$cache = wp_cache_get( $cache_key, '' );

		if ( ! $skip_cache && ! empty( $cache ) ) {
			return $cache;
		}

		if ( empty( $this->data ) || $skip_cache ) {
			$this->update_data( false, $skip_tokenizing );
		}

		// If the CLI is building the index, skip caching.
		if ( ! $skip_cache && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			wp_cache_set( $cache_key, $this->data, '', 1 );
		}

		return $this->data;
	}

	/**
	 * Export this Entry as its native Object.
	 *
	 * @since 4.0
	 * @return mixed
	 */
	public function native( $query = false ) {
		$native = $this->source->entry( $this, $query );

		if ( $query ) {
			$native = apply_filters( 'searchwp\entry\native', $native, $query );
		}

		return $native;
	}
}
