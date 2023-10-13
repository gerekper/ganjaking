<?php
/**
 * Class Notifier
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Notifier' ) ) {
	/**
	 * Class YITH_WCBK_Notifier
	 *
	 * @deprecated 5.0.0
	 */
	class YITH_WCBK_Notifier extends YITH_WCBK_Emails {
		/**
		 * The constructor.
		 */
		protected function __construct() {
			yith_wcbk_deprecated_function( 'YITH_WCBK_Notifier::__construct', '5.0.0' );
			parent::__construct();
		}
	}
}
