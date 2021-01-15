<?php
/**
 * weLaunch Class
 *
 * @class weLaunch_Class
 * @version 4.0.0
 * @package weLaunch Framework/Classes
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Class', false ) ) {

	/**
	 * Class weLaunch_Class
	 */
	class weLaunch_Class {

		/**
		 * Poiner to weLaunchFramework object.
		 *
		 * @var null|weLaunchFramework
		 */
		public $parent = null;

		/**
		 * Global arguments array.
		 *
		 * @var array|mixed|void
		 */
		public $args = array();

		/**
		 * Project opt_name
		 *
		 * @var mixed|string
		 */
		public $opt_name = '';

		/**
		 * weLaunch_Class constructor.
		 *
		 * @param null|weLaunchFramework $parent Pointer to weLaunchFramework object.
		 */
		public function __construct( $parent = null ) {
			if ( null !== $parent && is_object( $parent ) ) {
				$this->parent   = $parent;
				$this->args     = $parent->args;
				$this->opt_name = $this->args['opt_name'];
			}
		}

		/**
		 * Pointer to project specific weLaunchFramework object.
		 *
		 * @return null|object|weLaunchFramework
		 */
		public function core() {
			if ( isset( $this->opt_name ) && '' !== $this->opt_name ) {
				return weLaunch::instance( $this->opt_name );
			}

			return null;
		}

	}

}
