<?php
/**
 * Frontend Class for Tribe Events Calendar Events.
 *
 * @package WooCommerce Waitlist
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! class_exists( 'Pie_WCWL_Frontend_Event' ) ) {
	/**
	 * Loads up the waitlist for simple products
	 *
	 * @package  WooCommerce Waitlist
	 */
	class Pie_WCWL_Frontend_Event {

		/**
		 * Does current event have any out of stock tickets?
		 *
		 * @var boolean $has_out_of_stock_tickets
		 */
		public $has_out_of_stock_tickets = false;

		/**
		 * Load up hooks if product is out of stock
		 */
		public function init() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_event_script_data' ) );
		}

		/**
		 * Enqueue required scripts
		 */
		public function enqueue_event_script_data() {
			$data = $this->load_data_for_outputting_html_with_js();
			wp_localize_script( 'wcwl_frontend', 'wcwl_event_data', $data );
		}

		/**
		 * Save an array of out of stock WooCommerce tickets
		 */
		protected function load_data_for_outputting_html_with_js() {
			global $post;
			$html    = array( 'tickets' => array() );
			$tickets = Tribe__Tickets__Tickets::get_all_event_tickets( $post->ID );
			if ( $tickets ) {
				foreach ( $tickets as $ticket ) {
					if ( 0 === $ticket->stock && 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main' === $ticket->provider_class && wcwl_waitlist_is_enabled_for_product( $ticket->ID ) ) {
						$html['tickets'][ $ticket->ID ] = $this->get_checkbox_html( $ticket->ID );
						$this->has_out_of_stock_tickets = true;
					} else {
						$html['tickets'][ $ticket->ID ] = '';
					}
				}
				if ( $this->has_out_of_stock_tickets ) {
					$html['button'] = wcwl_get_waitlist_for_event( $post->ID, 'update' );
				}
			}
			return $html;
		}

		/**
		 * Get HTML for the checkbox elements for the event
		 *
		 * @param int $product_id current ticket ID.
		 *
		 * @return string $html HTML for waitlist checkbox
		 */
		protected function get_checkbox_html( $product_id ) {
			global $sitepress;
			$lang = '';
			if ( isset( $sitepress ) ) {
				$lang      = wpml_get_language_information( null, $product_id )['language_code'];
				$ticket_id = wcwl_get_translated_main_product_id( $product_id );
			}
			$product = wc_get_product( $product_id );
			$html    = '';

			return apply_filters( 'wcwl_ticket_checkbox_html', wcwl_get_waitlist_checkbox( $product, $lang ) );
		}
	}
}
