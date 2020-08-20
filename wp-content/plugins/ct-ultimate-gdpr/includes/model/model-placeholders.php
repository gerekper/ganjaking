<?php

/**
 * Class CT_Ultimate_GDPR_Model_Placeholders
 */
class CT_Ultimate_GDPR_Model_Placeholders {

	/**
	 * @var self
	 */
	private static $instance;

	/**
	 * @var array
	 */
	private $placeholders = array();

	/**
	 * CT_Ultimate_GDPR_Model_Placeholders constructor.
	 */
	private function __construct(  ) {

	}

	/** Add a placeholder pair: key => value
	 *
	 * @param string $placeholder
	 * @param string $value
	 */
	public function add( $placeholder, $value ) {
		if ( $placeholder && is_string( $placeholder ) && is_string( $value ) ) {
			$this->placeholders[ $placeholder ] = $value;
		}
	}

	/**
	 * Reset placeholders arrray
	 */
	public function clear(  ) {
		$this->placeholders = array();
		return $this;
	}

	/**
	 * @return array
	 */
	public function get(  ) {
		return $this->placeholders;
	}

	/**
	 * @param array $placeholders
	 *
	 * @return $this
	 */
	public function set( $placeholders ) {
		$this->placeholders = $placeholders;
		return $this;
	}

	/**
	 * @return CT_Ultimate_GDPR_Model_Placeholders
	 */
	public static function instance(  ) {
		 if ( ! self::$instance ) {
		 	self::$instance = new self;
		 }
		 return self::$instance;
	}

	/**
	 * @param $source
	 *
	 * @return mixed
	 */
	public function replace( $source ) {
		$string = apply_filters(
			'ct_ultimate_gdpr_placeholder_replace',
			str_replace( array_keys( $this->placeholders ), array_values( $this->placeholders ), $source ),
			$source,
			$this->placeholders
		);
		return do_shortcode( $string );
	}
}