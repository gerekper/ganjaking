<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Vc_Automap_Model' ) ) {
	/**
	 * Shortcode as model for automapper. Provides crud functionality for storing data for shortcodes that mapped by ATM
	 *
	 * @see Vc_Automapper
	 * @since 4.1
	 */
	#[\AllowDynamicProperties]
	class Vc_Automap_Model {
		/**
		 * @var string
		 */
		protected static $option_name = 'vc_automapped_shortcodes';
		/**
		 * @var
		 */
		protected static $option_data;
		/**
		 * @var array|bool
		 */
		public $id = false;
		public $tag;
		/**
		 * @var mixed
		 */
		protected $data;
		/**
		 * @var array
		 */
		protected $vars = array(
			'tag',
			'name',
			'category',
			'description',
			'params',
		);
		public $name;

		/**
		 * @param $data
		 */
		public function __construct( $data ) {
			$this->loadOptionData();
			$this->id = is_array( $data ) && isset( $data['id'] ) ? esc_attr( $data['id'] ) : $data;
			if ( is_array( $data ) ) {
				$this->data = stripslashes_deep( $data );
			}
			foreach ( $this->vars as $var ) {
				$this->{$var} = $this->get( $var );
			}
		}

		/**
		 * @return array
		 */
		public static function findAll() {
			self::loadOptionData();
			$records = array();
			foreach ( self::$option_data as $id => $record ) {
				$record['id'] = $id;
				$model = new self( $record );
				if ( $model ) {
					$records[] = $model;
				}
			}

			return $records;
		}

		/**
		 * @return array|mixed
		 */
		final protected static function loadOptionData() {
			if ( is_null( self::$option_data ) ) {
				self::$option_data = get_option( self::$option_name );
			}
			if ( ! self::$option_data ) {
				self::$option_data = array();
			}

			return self::$option_data;
		}

		/**
		 * @param $key
		 *
		 * @return null
		 */
		public function get( $key ) {
			if ( is_null( $this->data ) ) {
				$this->data = isset( self::$option_data[ $this->id ] ) ? self::$option_data[ $this->id ] : array();
			}

			return isset( $this->data[ $key ] ) ? $this->data[ $key ] : null;
		}

		/**
		 * @param $attr
		 * @param null $value
		 */
		public function set( $attr, $value = null ) {
			if ( is_array( $attr ) ) {
				foreach ( $attr as $key => $value ) {
					$this->set( $key, $value );
				}
			} elseif ( ! is_null( $value ) ) {
				$this->{$attr} = $value;
			}
		}

		/**
		 * @return bool
		 */
		public function save() {
			if ( ! $this->isValid() ) {
				return false;
			}
			foreach ( $this->vars as $var ) {
				$this->data[ $var ] = $this->{$var};
			}

			return $this->saveOption();
		}

		/**
		 * @return bool
		 */
		public function delete() {
			return $this->deleteOption();
		}

		/**
		 * @return bool
		 */
		public function isValid() {
			if ( ! is_string( $this->name ) || empty( $this->name ) ) {
				return false;
			}
			if ( ! preg_match( '/^\S+$/', $this->tag ) ) {
				return false;
			}

			return true;
		}

		/**
		 * @return bool
		 */
		protected function saveOption() {
			self::$option_data[ $this->id ] = $this->data;

			return update_option( self::$option_name, self::$option_data );
		}

		/**
		 * @return bool
		 */
		protected function deleteOption() {
			unset( self::$option_data[ $this->id ] );

			return update_option( self::$option_name, self::$option_data );
		}
	}
}
