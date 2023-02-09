<?php

namespace GFML\Compatibility\FeedAddon;

use GFML_String_Name_Helper;

final class TranslatableKey {
	/** @var string[] */
	private $path;

	/** @var string */
	private $title;

	/** @var string */
	private $kind;

	/**
	 * @param array  $path Path to value.
	 * @param string $title
	 * @param string $kind
	 */
	public function __construct( $path, $title, $kind ) {
		$this->path  = $path;
		$this->title = $title;
		$this->kind  = $kind;
	}

	/**
	 * Create an instance
	 *
	 * @param string|array $path
	 * @param string|null  $title
	 * @param string|null  $kind
	 * @return static
	 */
	public static function create( $path, $title = null, $kind = 'LINE' ) {
		$path  = (array) $path;
		$title = $title ?: implode( '-', $path );
		return new static( $path, $title, $kind );
	}

	/**
	 * Iterate `$path` as nested keys/nodes for the `$feedMeta` array and collect (1) end string values and (2) indexed path to that value.
	 * This function calls itself recursively until either:
	 * 1. The `$path` is complete (`$node == null`)
	 * 2. The `$path` was not found
	 * 3. Final value is not a valid string (`$path` maybe incorrect!)
	 *
	 * We expect that from this output (`Value[]`) we can get a final output as if we wrote out:
	 *
	 * ```php
	 * // $title = 'routing-[]'; $path = [ 'routing', '[]', 'value' ];
	 * $registeredStrings['routing-0'] = $feedMeta['routing'][0]['value'];
	 * $registeredStrings['routing-1'] = $feedMeta['routing'][1]['value'];
	 *
	 * // $title = 'confirmation_message'; $path = [ 'confirmation_messageValue' ];
	 * $registeredStrings['confirmation_message'] = $feedMeta['confirmation_messageValue'];
	 * ```
	 *
	 * @param array         $feedMeta
	 * @param int           $feedId
	 * @param string[]|null $path
	 * @param int[]         $indexes
	 *
	 * @return Value[]
	 */
	public function getValues( $feedMeta, $feedId, $path = null, $indexes = [] ) {
		$path = null === $path ? $this->path : $path;
		$node = isset( $path[0] ) ? $path[0] : null;
		if ( null === $node ) {
			if ( ! is_string( $feedMeta ) ) {
				// path is incorrect. Fail gracefully.
				return [];
			}
			// path is complete!
			return [ $this->makeValue( $feedMeta, $feedId, $indexes ) ];
		}

		if ( is_array( $feedMeta ) ) {
			if ( '[]' === $node ) {
				// path is an indexed array so continue for each child.
				$values = [];
				foreach ( $feedMeta as $index => $sub_meta ) {
					$values = array_merge( $values, $this->getValues( $sub_meta, $feedId, array_slice( $path, 1 ), $indexes + [ $index ] ) );
				}
				return $values;
			} elseif ( isset( $feedMeta[ $node ] ) ) {
				// path continues i.e. is deeper!
				return $this->getValues( $feedMeta[ $node ], $feedId, array_slice( $path, 1 ), $indexes );
			}
		}

		// path is not found or missing!
		return [];
	}

	/**
	 * @param string $value
	 * @param int    $feedId
	 * @param int[]  $indexes
	 *
	 * @return Value
	 */
	protected function makeValue( $value, $feedId, $indexes ) {
		$pathTitle = $this->getPathTitle( $indexes );
		$name      = $this->getStringName( $feedId, $pathTitle );
		$title     = ucfirst( str_replace( '_', ' ', $pathTitle ) );
		return new Value( $value, $name, $title, $this->kind );
	}

	/**
	 * Get title relevant to path by replacing [] placeholders with correct index.
	 *
	 * @param int[] $indexes
	 * @return string
	 */
	private function getPathTitle( $indexes ) {
		$count   = substr_count( $this->title, '[]' );
		$search  = array_fill( 0, $count, '[]' );
		$replace = array_slice( $indexes, 0, $count );
		return str_replace( $search, $replace, $this->title );
	}

	/**
	 * @param int    $feedId
	 * @param string $pathTitle
	 *
	 *  @return string */
	private function getStringName( $feedId, $pathTitle ) {
		$snh = new GFML_String_Name_Helper();
		return $snh->sanitize_string( $feedId . '-' . $pathTitle );
	}
}
