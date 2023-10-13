<?php
/**
 * Class YITH_WCBK_Wpml_Search_Forms
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Wpml_Search_Forms
 *
 * @since   1.0.10
 */
class YITH_WCBK_Wpml_Search_Forms {
	/**
	 * Single intance of the class.
	 *
	 * @var YITH_WCBK_Wpml_Search_Forms
	 */
	private static $instance;

	/**
	 * WPML Integration instance.
	 *
	 * @var YITH_WCBK_Wpml_Integration
	 */
	public $wpml_integration;

	/**
	 * Singleton implementation
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration WPML Integration instance.
	 *
	 * @return YITH_WCBK_Wpml_Search_Forms
	 */
	public static function get_instance( $wpml_integration ) {
		return ! is_null( self::$instance ) ? self::$instance : self::$instance = new static( $wpml_integration );
	}

	/**
	 * Constructor
	 *
	 * @param YITH_WCBK_Wpml_Integration $wpml_integration WPML Integration instance.
	 */
	private function __construct( $wpml_integration ) {
		$this->wpml_integration = $wpml_integration;

		add_action( 'yith_wcbk_booking_search_form_after_print_fields', array( $this, 'add_language_hidden_input' ) );
		add_action( 'yith_wcbk_search_booking_products_search_args', array( $this, 'fix_search_args' ) );
	}

	/**
	 * Add language hidden input in search forms
	 */
	public function add_language_hidden_input() {
		$lang = $this->wpml_integration->current_language;
		echo '<input type="hidden" name="lang" value="' . esc_attr( $lang ) . '">';
	}

	/**
	 * Fix search args by setting parent terms
	 *
	 * @param array $search_args Search arguments.
	 *
	 * @return array
	 */
	public function fix_search_args( $search_args ) {
		if ( isset( $search_args['tax_query'] ) ) {
			foreach ( $search_args['tax_query'] as $key => $value ) {
				if ( isset( $value['taxonomy'] ) && ! empty( $value['field'] ) && 'term_id' === $value['field'] && ! empty( $value['terms'] ) && is_array( $value['terms'] ) ) {
					$original_terms = array();
					foreach ( $value['terms'] as $id ) {
						$original_id      = $this->wpml_integration->get_original_term_id( $id, $value['taxonomy'] );
						$original_terms[] = ! ! $original_id ? $original_id : $id;
					}

					$search_args['tax_query'][ $key ]['terms'] = $original_terms;
				}
			}
		}

		return $search_args;
	}
}
