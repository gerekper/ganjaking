<?php
/**
 * Styled Variations Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Styled Variations Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_variations extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @param array $element The element array.
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {

		$current_builder = $element['builder'];

		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			$id_for_meta              = $args['product_id'];
			$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $args['product_id'] );
			if ( ! $wpml_is_original_product ) {
				$id_for_meta = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $args['product_id'] ) );
			}

			$builder = themecomplete_get_post_meta( $id_for_meta, 'tm_meta', true );

			if ( ! $current_builder ) {

				if ( ! isset( $builder['tmfbuilder'] ) ) {
					$builder['tmfbuilder'] = [];
				}
				$current_builder = $builder['tmfbuilder'];

			}
		}

		$variations_disabled = is_array( $current_builder ) && isset( $current_builder['variations_disabled'] ) ? $current_builder['variations_disabled'] : '';
		$display             = [
			'builder'                    => $current_builder,
			'variation_section_disabled' => $variations_disabled,
		];

		return apply_filters( 'wc_epo_display_field_variations', $display, $this, $element, $args );
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {
		return [
			'passed'  => true,
			'message' => false,
		];
	}

}
