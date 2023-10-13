<?php
/**
 * Class YITH_WCBK_Badge_Management_Integration
 * Badge Management integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Badge_Management_Integration
 *
 * @since   1.0.1
 */
class YITH_WCBK_Badge_Management_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Init
	 */
	protected function init() {
		if ( $this->is_enabled() ) {
			add_filter( 'yith_wcbk_search_form_result_product_thumb_wrapper', array( $this, 'add_badges_in_search_form_results' ), 10, 2 );
		}
	}

	/**
	 * Adds badges in Search Form results
	 *
	 * @param string $html       Thumb wrapper HTML.
	 * @param int    $product_id Product ID.
	 *
	 * @return string
	 */
	public function add_badges_in_search_form_results( $html, $product_id ) {
		return apply_filters( 'yith_wcbm_product_thumbnail_container', $html, $product_id );
	}
}
