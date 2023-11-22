<?php
/**
 * class-cache-object.php
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is provided subject to the license granted.
 * Unauthorized use and distribution is prohibited.
 * See COPYRIGHT.txt and LICENSE.txt
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package woocommerce-product-search
 * @since 5.0.0
 */

namespace com\itthinx\woocommerce\search\engine;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Encapsulates cache items.
 *
 * @property string $key
 * @property mixed $value
 * @property int $created
 * @property int $lifespan
 * @property string $hash
 */
class Cache_Object {

	/**
	 * Cache object key
	 *
	 * @var string
	 */
	private $key = null;

	/**
	 * Cached object value
	 *
	 * @var mixed
	 */
	private $value = null;

	/**
	 * Timestamp of creation
	 *
	 * @var int
	 */
	private $created = null;

	/**
	 * Lifespan of the cache object, period of time after which it expires.
	 *
	 * @var int
	 */
	private $lifespan = null;

	/**
	 * Value hash
	 *
	 * @var string
	 */
	private $hash = null;

	/**
	 * Creates a cache object which holds the value for the given key.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param int $lifespan
	 */
	public function __construct( $key, $value, $lifespan = null ) {

		if ( is_string( $key ) ) {
			$this->key = $key;
		}
		$this->set_value( $value );
		$this->created = time();
		$this->set_lifespan( $lifespan );
	}

	/**
	 * Get the object's key.
	 *
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * Get the object's value.
	 *
	 * @return mixed
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Get the object's hash.
	 *
	 * @return string
	 */
	public function get_hash() {
		return $this->hash;
	}

	/**
	 * Get the creation timestamp.
	 *
	 * @return int
	 */
	public function get_created() {
		return $this->created;
	}

	/**
	 * Get the lifespan.
	 *
	 * @return int
	 */
	public function get_lifespan() {
		return $this->lifespan;
	}

	/**
	 * Set the key.
	 *
	 * @param string $key
	 */
	public function set_key( $key ) {
		if ( is_string( $key ) ) {
			$this->key = $key;
		}
	}

	/**
	 * Set the value.
	 *
	 * @param mixed $value
	 */
	public function set_value( $value ) {
		$this->value = $value;
		$json = json_encode( $value, JSON_PARTIAL_OUTPUT_ON_ERROR );

		$this->hash = hash( 'crc32', $json );
	}

	/**
	 * Set the creation timestamp.
	 *
	 * @param int $created a positive integer or zero
	 */
	public function set_created( $created ) {
		if ( $created !== null && is_numeric( $created ) ) {
			$created = intval( $created );
			if ( $created <= 0 ) {
				$created = 0;
			}
		} else {
			$created = 0;
		}
		$this->created = $created;
	}

	/**
	 * Set the lifespan.
	 *
	 * @param int $lifespan a positive integer or null
	 */
	public function set_lifespan( $lifespan ) {
		if ( $lifespan !== null && is_numeric( $lifespan ) ) {
			$lifespan = intval( $lifespan );
			if ( $lifespan <= 0 ) {
				$lifespan = null;
			}
		} else {
			$lifespan = null;
		}
		$this->lifespan = $lifespan;
	}

	/**
	 * Whether this cache object has expired.
	 *
	 * @return boolean
	 */
	public function has_expired() {
		$expired = Cache_Control::has_timestamp_expired( $this->created );
		if ( !$expired ) {
			if ( $this->lifespan !== null ) {
				if ( time() > $this->created + $this->lifespan ) {
					$expired = true;
				}
			}
		}
		return $expired;
	}

	/**
	 * Provide a measure of the magnitude of the object's value.
	 *
	 * @return int
	 */
	public function get_magnitude() {

		$magnitude = 0;
		$type = gettype( $this->value );
		switch ( $type ) {
			case 'boolean':
				$magnitude = 1;
				break;
			case 'integer':
				$magnitude = 1;
				break;
			case 'double':
				$magnitude = 1;
				break;
			case 'string':
				$magnitude = strlen( $this->value );
				break;
			case 'array':
				$magnitude = count( $this->value );
				break;
			case 'object':
				$magnitude = strlen( serialize( $this->value ) );
				break;
			case 'resource':
				$magnitude = 1;
				break;
			case 'resource (closed)':
				$magnitude = 1;
				break;
			case 'NULL':
				break;
			case 'unknown type':
				break;
		}
		return $magnitude;
	}
}
