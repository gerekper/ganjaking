<?php

class WC_Dynamic_Pricing_Adjustment_Set_Category extends WC_Dynamic_Pricing_Adjustment_Set {

	public $targets;
	public $is_valid_rule = false;
	public $is_valid_for_user = false;

	public function __construct( $set_id, $set_data ) {
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
		if ( $this->targets && is_array( $this->targets ) ) {
			$this->is_valid_rule = count( $this->targets ) > 0;
		} else {
			$this->is_valid_rule = false;
		}


		add_action( 'init', array( $this, 'on_init' ), 0 );
		add_action( 'wc_dynamic_pricing_counter_updated', array( $this, 'check_is_valid_rule' ) );
	}

	public function on_init() {
		$this->is_valid_for_user = $this->is_valid_for_user();
	}

	public function check_is_valid_rule() {
		if ( isset( $this->set_data['collector']['args'] ) ) {
			if ( WC_Dynamic_Pricing_Counter::categories_in_cart( $this->set_data['collector']['args']['cats'] ) || WC_Dynamic_Pricing_Counter::categories_in_cart( $this->targets ) ) {
				$this->is_valid_rule = true;
			}
		}
	}


	/**
	 * @return WC_Dynamic_Pricing_Collector_Category
	 */
	public function get_collector_object() {
		$collector_obj = apply_filters( 'wc_dynamic_pricing_get_collector_object', 'WC_Dynamic_Pricing_Collector_Category', 'category' );

		return new $collector_obj( $this->set_data['collector'] );
	}

}
