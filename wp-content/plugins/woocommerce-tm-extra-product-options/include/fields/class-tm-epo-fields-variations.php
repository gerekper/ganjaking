<?php
/**
 * Styled Variations Field class
 *
 * @package Extra Product Options/Fields
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_FIELDS_variations extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @since 1.0
	 */
	public function display_field( $element = array(), $args = array() ) {

		$current_builder = $element['builder'];

		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			$id_for_meta              = $args['product_id'];
			$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $args['product_id'] );
			if ( ! $wpml_is_original_product ) {
				$id_for_meta = floatval( THEMECOMPLETE_EPO_WPML()->get_original_id( $args['product_id'] ) );
			}

			$builder = themecomplete_get_post_meta( $id_for_meta, 'tm_meta', TRUE );

			if ( ! $current_builder ) {

				if ( ! isset( $builder['tmfbuilder'] ) ) {
					$builder['tmfbuilder'] = array();
				}
				$current_builder = $builder['tmfbuilder'];

			}

		}

		$variations_disabled = is_array( $current_builder ) &&
		                       isset( $current_builder['variations_disabled'] )
			? $current_builder['variations_disabled'] : "";
		$display             = array(
			'builder'                    => $current_builder,
			'variation_section_disabled' => $variations_disabled,
		);

		return $display;
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {
		return array( 'passed' => TRUE, 'message' => FALSE );
	}

}