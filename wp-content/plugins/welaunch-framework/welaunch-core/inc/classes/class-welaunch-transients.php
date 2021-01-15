<?php
/**
 * weLaunch Transients Class
 *
 * @class weLaunch_Transients
 * @version 4.0.0
 * @package weLaunch Framework
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'weLaunch_Transients', false ) ) {

	/**
	 * Class weLaunch_Transients
	 */
	class weLaunch_Transients extends weLaunch_Class {

		/**
		 * Get transients from database.
		 */
		public function get() {
			$core = $this->core();

			if ( empty( $core->transients ) ) {
				$core->transients = get_option( $core->args['opt_name'] . '-transients', array() );
			}
		}

		/**
		 * Set transients in database.
		 */
		public function set() {
			$core = $this->core();

			if ( ! isset( $core->transients ) || ! isset( $core->transients_check ) || $core->transients_check !== $core->transients ) {
				update_option( $core->args['opt_name'] . '-transients', $core->transients );
			}
		}
	}
}
