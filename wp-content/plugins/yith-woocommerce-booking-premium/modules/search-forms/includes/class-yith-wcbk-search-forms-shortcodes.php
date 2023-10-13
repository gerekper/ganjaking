<?php
/**
 * Class YITH_WCBK_Search_Forms_Shortcodes
 * Handle shortcodes for the Search Forms module.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\SearchForms
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Forms_Shortcodes' ) ) {
	/**
	 * YITH_WCBK_Search_Forms_Shortcodes class.
	 */
	class YITH_WCBK_Search_Forms_Shortcodes {
		use YITH_WCBK_Singleton_Trait;

		/**
		 * YITH_WCBK_Search_Forms_Shortcodes constructor.
		 */
		protected function __construct() {
			add_shortcode( 'booking_search_form', array( $this, 'booking_search_form' ) );
		}

		/**
		 * Booking search form
		 *
		 * @param array $atts Attributes.
		 *
		 * @return string
		 */
		public function booking_search_form( $atts ) {
			ob_start();
			$form_id = $atts['id'] ?? 0;
			$form    = ! ! $form_id ? yith_wcbk_get_search_form( $form_id ) : false;
			if ( $form ) {
				$form->output( $atts );
			}

			return ob_get_clean();
		}
	}
}
