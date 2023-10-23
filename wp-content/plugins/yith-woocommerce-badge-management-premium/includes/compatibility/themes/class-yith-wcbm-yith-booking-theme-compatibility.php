<?php
/**
 * YITH Booking Theme Compatibility Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManegement\Compatibility
 */

defined( 'YITH_WCBM' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBM_YITH_Booking_Theme_Compatibility' ) ) {
	/**
	 * YITH Booking Theme Compatibility Class
	 */
	class YITH_WCBM_YITH_Booking_Theme_Compatibility {
		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_YITH_Booking_Theme_Compatibility
		 */
		protected static $instance;

		/**
		 * Are we in header?
		 *
		 * @var bool
		 */
		protected $in_header = false;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBM_YITH_Booking_Theme_Compatibility
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCBM_YITH_Booking_Theme_Compatibility constructor.
		 */
		private function __construct() {
			add_filter( 'yith_wcbm_is_allowed_badge_showing', array( $this, 'check_badge_allowed_in_product_pages' ), 10, 1 );

			add_action( 'yith_booking_content_header', array( $this, 'set_in_header' ), 0 );
			add_action( 'yith_booking_content_header', array( $this, 'unset_in_header' ), 9999 );
		}

		/**
		 * Set in-header
		 */
		public function set_in_header() {
			$this->in_header = true;
		}

		/**
		 * Unset in-header
		 */
		public function unset_in_header() {
			$this->in_header = false;
		}

		/**
		 * Check badge allowed in product pages
		 *
		 * @param bool $allowed Check is allowed.
		 *
		 * @return bool
		 */
		public function check_badge_allowed_in_product_pages( $allowed ) {
			if ( $allowed && $this->in_header ) {
				$hide_on_single = get_option( 'yith-wcbm-hide-on-single-product', 'no' ) === 'yes';
				if ( $hide_on_single && function_exists( 'is_product' ) && is_product() ) {
					$allowed = false;
				}
			}

			return $allowed;
		}
	}
}
