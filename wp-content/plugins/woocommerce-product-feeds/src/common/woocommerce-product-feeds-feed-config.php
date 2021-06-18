<?php

class WoocommerceProductFeedsFeedConfig {

	const DEFAULT_CONFIG = [
		'id'              => '',
		'type'            => '',
		'name'            => '',
		'category_filter' => '',
		'categories'      => [],
		'start'           => 0,
		'limit'           => -1,
	];

	/**
	 * The config settings.
	 * @var array
	 */
	private $config;

	/**
	 * WoocommerceProductFeedsFeedConfig constructor.
	 */
	public function __construct() {
		$this->config = apply_filters( 'woocommerce_gpf_feed_config_default', self::DEFAULT_CONFIG );
	}

	/**
	 * @param string $type
	 */
	public function set_type( $type ) {
		$this->config['type'] = $type;
	}

	/**
	 * @param string $category_filter
	 */
	public function set_category_filter( $category_filter ) {
		$this->config['category_filter'] = $category_filter;
	}

	/**
	 * @param array $categories
	 */
	public function set_categories( $categories ) {
		$this->config['categories'] = $categories;
	}

	/**
	 * @param int $start
	 */
	public function set_start( $start ) {
		$this->config['start'] = $start;
	}

	/**
	 * @param string $limit
	 */
	public function set_limit( $limit ) {
		$this->config['limit'] = $limit;
	}

	/**
	 * @param string $name
	 */
	public function set_name( $name ) {
		$this->config['name'] = $name;
	}

	/**
	 * @param string $feed_id
	 */
	public function set_id( $feed_id ) {
		$this->config['id'] = $feed_id;
	}

	/**
	 * Magic setter for non-core config properties.
	 *
	 * @param $key
	 * @param $value
	 */
	public function __set( $key, $value ) {
		$setter = 'set_' . $key;
		if ( is_callable( [ $this, $setter ] ) ) {
			$this->$setter( $value );

			return;
		}
		$valid_keys = apply_filters( 'woocommerce_gpf_feed_config_valid_extra_keys', [] );
		if ( in_array( $key, $valid_keys, true ) ) {
			$this->config[ $key ] = $value;
		}
	}

	/**
	 * Magic getter.
	 *
	 * @param $key
	 *
	 * @return mixed
	 * @throws Exception
	 */
	public function __get( $key ) {
		if ( isset( $this->config[ $key ] ) ) {
			return $this->config[ $key ];
		}
		$valid_keys = apply_filters( 'woocommerce_gpf_feed_config_valid_extra_keys', [] );
		if ( in_array( $key, $valid_keys, true ) ) {
			return null;
		}
		throw new Exception( 'Attempt to retrieve invalid config key' );
	}

	/**
	 * @return array
	 */
	public function to_array() {
		return $this->config;
	}

	/**
	 * Gets an HTML-safe summary of the feed.
	 *
	 * @return string
	 */
	public function get_readable_summary() {
		$summary = '';
		$summary = '<strong>' . esc_html( $this->config['name'] ) . "</strong>\n<br>";
		foreach ( $this->config as $key => $value ) {
			if ( in_array( $key, [ 'id', 'name', 'start' ], true ) ) {
				continue;
			}
			if ( 'limit' === $key && -1 === $value ) {
				continue;
			}
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}
			$key = ucfirst( str_replace( '_', ' ', $key ) );
			if ( empty( $value ) ) {
				$value = '-';
			}
			$summary .= '&nbsp;&nbsp;' . esc_html( $key ) . ': ' . esc_html( $value ) . "\n<br>";
		}

		return $summary;
	}
}
