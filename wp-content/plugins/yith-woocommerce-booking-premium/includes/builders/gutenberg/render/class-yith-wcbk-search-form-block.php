<?php
/**
 * Handle "Search form" Gutenberg block.
 *
 * @package YITH\Booking\Classes
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Form_Block' ) ) {
	/**
	 * Search form block class
	 *
	 * @since 5.0.0
	 */
	class YITH_WCBK_Search_Form_Block extends YITH_WCBK_Render_Block_With_Style {

		/**
		 * Block attributes
		 *
		 * @var array
		 */
		protected $attributes = array(
			'searchFormId' => 0,
		);

		/**
		 * Get the search form ID.
		 *
		 * @return string
		 */
		public function get_search_form_id() {
			return $this->attributes['searchFormId'];
		}

		/**
		 * Render
		 */
		public function render() {
			$search_form_id = $this->get_search_form_id();
			$form           = ! ! $search_form_id ? yith_wcbk_get_search_form( $search_form_id ) : false;
			if ( $form ) {
				$form->output();
			} else {
				if ( $this->is_blank_state_allowed() ) {
					$this->render_blank_state();
				}
			}
		}

		/**
		 * Retrieve blank state params.
		 *
		 * @return array
		 */
		public function get_blank_state_params() {
			return array(
				'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
				'message'  => __( 'Please, select a valid search form!', 'yith-booking-for-woocommerce' ),
			);
		}
	}
}
