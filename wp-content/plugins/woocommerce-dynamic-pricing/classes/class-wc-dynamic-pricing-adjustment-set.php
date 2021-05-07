<?php

class WC_Dynamic_Pricing_Adjustment_Set {

	protected $set_data;
	public $set_id;
	public $mode;
	public $pricing_rules;
	public $targets;

	public function __construct( $set_id, $set_data ) {
		$this->set_id   = $set_id;
		$this->set_data = $set_data;

		if ( isset( $set_data['mode'] ) && $set_data['mode'] == 'block' ) {
			$this->mode = 'block';

			if ( ! empty( $set_data['blockrules'] ) ) {
				$this->pricing_rules = $set_data['blockrules'];
			}
		} else {
			$this->mode = 'bulk';

			$this->pricing_rules = $set_data['rules'];
		}


	}

	public function is_targeted_product( $product_id, $variation_id = false ) {
		return false;
	}

	public function is_valid_for_user() {
		$result             = 0;
		$pricing_conditions = $this->set_data['conditions'];

		if ( is_array( $pricing_conditions ) && sizeof( $pricing_conditions ) > 0 ) {
			$conditions_met = 0;

			foreach ( $pricing_conditions as $condition ) {
				switch ( $condition['type'] ) {
					case 'apply_to':
						if ( is_array( $condition['args'] ) && isset( $condition['args']['applies_to'] ) ) {
							if ( $condition['args']['applies_to'] == 'everyone' ) {
								$result = 1;
							} elseif ( $condition['args']['applies_to'] == 'unauthenticated' ) {
								if ( ! is_user_logged_in() ) {
									$result = 1;
								}
							} elseif ( $condition['args']['applies_to'] == 'authenticated' ) {
								if ( is_user_logged_in() ) {
									$result = 1;
								}
							} elseif ( $condition['args']['applies_to'] == 'roles' && isset( $condition['args']['roles'] ) && is_array( $condition['args']['roles'] ) ) {
								if ( is_user_logged_in() ) {
									foreach ( $condition['args']['roles'] as $role ) {
										if ( current_user_can( $role ) ) {
											$result = 1;
											break;
										}
									}
								}
							}
						}
						break;
					default:
						$result = 0;
						break;
				}

				$result = apply_filters( 'woocommerce_dynamic_pricing_is_rule_set_valid_for_user', $result, $condition, $this );

				$conditions_met += $result;
			}

			$execute_rules = false;
			if ( $this->set_data['conditions_type'] == 'all' ) {
				$execute_rules = $conditions_met == count( $pricing_conditions );
			} elseif ( $this->set_data['conditions_type'] == 'any' ) {
				$execute_rules = $conditions_met > 0;
			}
		} else {
			//empty conditions - default match, process price adjustment rules
			$execute_rules = true;
		}

		if ( $execute_rules && ( isset( $this->set_data['date_from'] ) || isset( $this->set_data['date_to'] ) ) ) {
			// Check date range
			$execute_rules = wc_dynamic_pricing_is_within_date_range( $this->set_data['date_from'], $this->set_data['date_to'] );
		}

		return $execute_rules;
	}

	public function get_collector() {
		return $this->set_data['collector'];
	}

	/**
	 * @return WC_Dynamic_Pricing_Collector
	 */
	public function get_collector_object() {
		return new WC_Dynamic_Pricing_Collector( $this->set_data['collector'] );
	}

}
