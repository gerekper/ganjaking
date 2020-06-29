<?php

class WC_Dynamic_Pricing_Adjustment_Set_Taxonomy extends WC_Dynamic_Pricing_Adjustment_Set {

	public $targets;
	public $is_valid_rule = false;
	public $is_valid_for_user = false;

	public $taxonomy;

	public function __construct( $set_id, $set_data, $taxonomy ) {
	    $this->taxonomy = $taxonomy;

		parent::__construct( $set_id, $set_data );

		//Normalize the targeted items for version differences.
		$targets = false;
		if ( isset( $set_data['targets'] ) ) {
			$targets = $set_data['targets'];
		} else {
			//Backwards compatibility for v 1.x, target the collected quantities.
			$targets = isset( $set_data['collector']['args']['cats'] ) ? $set_data['collector']['args']['cats'] : false;
		}

		$this->targets = apply_filters( 'wc_dynamic_pricing_get_adjustment_set_targets', $targets, $this );
		$this->is_valid_rule &= !empty($this->targets) &&  count( $this->targets ) > 0;

		add_action( 'init', array($this, 'on_init'), 0 );
		add_action( 'wc_dynamic_pricing_counter_updated', array($this, 'check_is_valid_rule') );
	}

	public function on_init() {
		$this->is_valid_for_user = $this->is_valid_for_user();
	}

	public function check_is_valid_rule() {
		if ( isset( $this->set_data['collector']['args'] ) ) {
			if ( WC_Dynamic_Pricing_Counter::taxonomies_in_cart( $this->set_data['collector']['args']['cats'], $this->taxonomy ) || WC_Dynamic_Pricing_Counter::taxonomies_in_cart( $this->targets, $this->taxonomy ) ) {
				$this->is_valid_rule = true;
			}
		}
	}

}
