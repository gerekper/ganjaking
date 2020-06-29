<?php

class WC_Dynamic_Pricing_Adjustment_Set_Product extends WC_Dynamic_Pricing_Adjustment_Set {

	public $target_variations;

	public function __construct( $set_id, $set_data ) {
		parent::__construct( $set_id, $set_data );

		//Helper code to normalize the possibile variation arguments. 
		$variations = false;
		if ( isset( $set_data['variation_rules'] ) ) {
			$variation_rules = isset( $set_data['variation_rules'] ) ? $set_data['variation_rules'] : array();
			if ( isset( $variation_rules['args']['type'] ) && $variation_rules['args']['type'] == 'variations' ) {
				$variations = isset( $variation_rules['args']['variations'] ) ? $variation_rules['args']['variations'] : array();
			}
		}

		$this->target_variations = apply_filters( 'wc_dynamic_pricing_get_adjustment_set_variations', $variations, $this );
	}

}
