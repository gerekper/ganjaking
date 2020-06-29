<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YWAF_Rules' ) ) {

	/**
	 * Rules abstract class
	 *
	 * @class   YWAF_Rules
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 *
	 */
	abstract class YWAF_Rules {

		private $message;
		private $points;

		/**
		 * Constructor
		 *
		 * @since   1.0.0
		 *
		 * @param   $message string
		 * @param   $points  integer
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		function __construct( $message, $points ) {

			$this->message = $message;
			$this->points  = $points;

		}

		/**
		 * Get label
		 *
		 * @since   1.0.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_message() {
			return $this->message;
		}

		/**
		 * Get risk value
		 *
		 * @since   1.0.0
		 * @return  integer
		 * @author  Alberto Ruggiero
		 */
		public function get_points() {
			return $this->points;
		}

		/**
		 * Get risk value
		 *
		 * @since   1.0.0
		 *
		 * @param   $order WC_Order
		 *
		 * @return  boolean|mixed
		 * @author  Alberto Ruggiero
		 */
		public function get_fraud_risk( $order ) {
			die( 'function YWAF_Rules->get_fraud_risk() must be over-ridden in a sub-class.' );
		}

	}

}

