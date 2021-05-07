<?php

class WC_Dynamic_Pricing_Adjustment_Set_Totals extends WC_Dynamic_Pricing_Adjustment_Set {

	public $targets;

	public function __construct( $set_id, $set_data ) {
		parent::__construct( $set_id, $set_data );

		//Normalize the targeted items for version differences.
		$targets = false;
		if ( isset( $set_data['targets'] ) ) {
			$targets = $set_data['targets'];
		} else {
			$targets = array();
		}

		$this->targets = apply_filters( 'wc_dynamic_pricing_get_adjustment_set_targets', $targets, $this );
	}

}
